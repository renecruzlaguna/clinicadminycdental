<?php

namespace AppBundle\Libs\Controller;

use AppBundle\Libs\Normalizer\ResultDecorator;
use FOS\RestBundle\Controller\FOSRestController;

class BaseController extends FOSRestController {

    public function getAllDataOfModel($model, $decorator = ResultDecorator::DEFAULT_DECORATOR) {

        $repo = $this->getRepo($model);
        $result = $repo->findAll();
        return $this->normalizeResult($model, $result, $decorator);
    }

    public function normalizeResult($model, $result, $decorator = ResultDecorator::DEFAULT_DECORATOR) {
        $data = $this->get('manager.json')->normalize('normalizer.' . strtolower($model), $result, $decorator);
        $res = array('success' => true, 'data' => $data);
        return $res;
    }

    public function returnNullResponse() {
        return array('success' => false, 'data' => array(), 'error' => 'No se encontraron datos');
    }
    public function returnNotExistInLdapResponse() {
        return array('success' => false, 'data' => array(), 'error' => 'El usuario no existe en el Ldap configurado');
    }
    public function returnErrorLdapResponse() {
        return array('success' => false, 'data' => array(), 'error' => 'Ocurrió un error al consultar el Ldap configurado');
    }

    public function returnSecurityViolationResponse() {
        return array('success' => false, 'data' => array(), 'error' => 'No se encontr&oacute; la llave de seguridad en la cabecera de la petici&oacute;n','code'=>403);
    }

    public function returnDeniedResponse() {
        return array('success' => false, 'error' => 'No tiene permiso para ejecutar esta acci&oacute;n');
    }



    public function isGarantedKeyInCurrentRequest() {
        $request = $this->get('request_stack')->getMasterRequest();
        if ($request) {

            $token = $request->headers->get('apiKey');
            if($token){
           return $this->getRepo('Usuario')->findOneBy(array('token' => $token));
            }
        }
        return false;
    }

    public function getUserOfCurrentRequest() {
        $request = $this->get('request_stack')->getMasterRequest();
        if ($request) {
            $userRepo = $this->getRepo('Usuario');
            $token = $request->headers->get('apiKey');
            $user = $userRepo->findOneBy(array('token' => $token));
            return $user;
        }
        return null;
    }
    public function hasRole($role){
        $user=$this->getUserOfCurrentRequest();
        if($user){
            return $user->hasRoleName($role);
        }
        return false;
    }

    
    public function getDataOfModelById($model, $id, $decorator = ResultDecorator::DEFAULT_DECORATOR) {

        $repo = $this->getRepo($model);
        $result = $repo->find($id);

        if ($result == null) {
            return $this->returnNullResponse();
        }
        return $this->normalizeResult($model, $result, $decorator);
    }

    public function validateModel($entity){
        $validator=$this->get('validator');
        $errors=$validator->validate($entity);
        $fieldWithError=array();
        foreach($errors as $error){
            $fieldWithError[$error->getPropertyPath()] =$error->getMessage();
        }
        return $fieldWithError;
    }


    public function filterModel($entity, $start = 0, $limit = 30, $filters = array(), $columns_alias = array(), $decorator = ResultDecorator::DEFAULT_DECORATOR) {


        $repo = $this->getRepo($entity);

        $qb = $repo->getBaseQuery(strtolower($entity), $start, $limit, $filters, $columns_alias, $decorator);
        $result = $repo->getResult($qb);
        $total = $repo->doCount($qb);


        $data = $this->get('manager.json')->normalize('normalizer.' . strtolower($entity), $result, $decorator);
        $res = array('success' => true, 'data' => $data, 'total' => $total);


        return $res;
    }

    public function customFilterModel($entity, $customFunction, $params, $decorator = PrototypeDecorator::NESTED_OBJECTS) {
        $repo = $this->getRepo($entity);
        $qb = $repo->$customFunction($entity, $params, $decorator);
        $result = $repo->getResult($qb);
        $total = $repo->doCount($qb);
        $data = $this->get('manager.json')->normalize('normalizer.' . strtolower($entity), $result, $decorator);
        $res = array('success' => true, 'data' => $data, 'total' => $total);
        return $res;
    }

    public function removeModel($model, $id, $arrayPreDelete = array(), $arrayPostDelete = array(), $useTransaction = true) {
        $repo = $this->getRepo($model);
        try {

            $entity = $repo->find($id);
            if ($entity == null) {
                return $this->returnNullResponse();
            }

            if ($useTransaction)
                $repo->beginTransaction();
            foreach ($arrayPreDelete as $callPre) {
                if (is_array($callPre) && key_exists('class', $callPre) && key_exists('method', $callPre)) {
                    call_user_func_array(array($callPre['class'], $callPre['method']), array($entity));
                }
            }
            $this->getManager()->remove($entity);
            $this->getManager()->flush();
            if ($useTransaction)
                $repo->commit();

            foreach ($arrayPostDelete as $callPost) {
                if (is_array($callPost) && key_exists('class', $callPost) && key_exists('method', $callPost)) {
                    call_user_func_array(array($callPost['class'], $callPost['method']), array());
                }
            }

            return array('success' => true);
        } catch (\Exception $exception) {
            $class = get_class($exception);
            if ($useTransaction)
                $repo->rollback();
            $this->resetManager();
            return $this->manageException($exception);
        }
    }

    /*
     * Metodo para la entidad pasada por parametros
     * */

    public function removeEntityModel($model, $entity, $arrayPreDelete = array(), $arrayPostDelete = array(), $useTransaction = true) {
        $repo = $this->getRepo($model);
        try {

            if ($entity == null) {
                return $this->returnNullResponse();
            }
            if ($useTransaction)
                $repo->beginTransaction();
            foreach ($arrayPreDelete as $callPre) {
                if (is_array($callPre) && key_exists('class', $callPre) && key_exists('method', $callPre)) {
                    call_user_func_array(array($callPre['class'], $callPre['method']), array($entity));
                }
            }

            $this->getManager()->remove($entity);
            $this->getManager()->flush();
            if ($useTransaction)
                $repo->commit();

            foreach ($arrayPostDelete as $callPost) {
                if (is_array($callPost) && key_exists('class', $callPost) && key_exists('method', $callPost)) {
                    call_user_func_array(array($callPost['class'], $callPost['method']), array());
                }
            }


            return array('success' => true);
        } catch (\Exception $exception) {
            $class = get_class($exception);
            if ($useTransaction)
                $repo->rollback();
            $this->resetManager();

            return $this->manageException($exception);
        }
    }

    public function extraValidate($data, $objectPersist, $extraValidate = array()) {
        /* para especificar un validador comun 
         * que no sea necesariamente la clase 
         * que estoy guardando
         * nombrevalidador=>array(ValidationTypes)
         */

        foreach ($extraValidate as $validatorName => $validationsTypes) {
            $result = $this->get('manager.validator')->validate($validatorName, $data, $objectPersist, $validationsTypes);
            if (!is_string($result)) {
                throw new LocalException("E14", array('validator.' . $validatorName));
            } else if (strlen($result) > 0) {
                throw new LocalException("E11", array($result));
            }
        }
        return true;
    }

    public function validate($objectPersist) {
        $errors = $this->get('validator')->validate($objectPersist);
        if (count($errors) > 0) {
            $message = $this->get('translator')->trans($errors[0]->getMessage());
            throw new \Exception($message);
        }


        return true;
    }

    public function getManager($name = null) {
        $manager = $this->getDoctrine()->getManager($name);
        if ($manager != null) {
            if (!$manager->isOpen()) {
                $this->resetManager($name);
            }
        }
        return $this->getDoctrine()->getManager($name);
    }

    public function resetManager($name = null) {
        $manager = $this->getDoctrine()->getManager($name);
        if ($manager != null) {
            $this->getDoctrine()->resetManager($name);
        }
    }


    public function populateModel($model,array $data){
        $repo = $this->getRepo($model);
        $class = $repo->getClassName();
        $manager = $this->getManager();
        $entity = NULL;
        if (isset($data['id'])) {
            $entity = $repo->find($data['id']);
            if ($entity == null) {
                return $this->returnNullResponse();
            }
        } else {
            $entity = new $class();

        }
        $entity = $repo->populateEntity($data);
        return $entity;
    }

    public function saveEntity($entity) {


        $manager = $this->getManager();

        try {

            $manager->persist($entity);
            $manager->flush();

            return true;
        } catch (\Exception $exception) {
         $this->resetManager();


            return $this->manageException($exception);
        }
    }


    public function saveModel($model, array $data, $extraValidation = array(), $useTransaction = true, $arrayPreSave = array(), $arrayPostSave = array()) {

        $repo = $this->getRepo($model);
        $class = $repo->getClassName();
        $manager = $this->getManager();
//        $user = $this->getUserOfCurrentRequest();
//        if (!$user) {
//            return $this->returnSecurityViolationResponse();
//        }
        $entity = NULL;
        try {
            if ($useTransaction)
                $repo->beginTransaction();
            if (isset($data['id'])) {
                $entity = $repo->find($data['id']);
                if ($entity == null) {
                    return $this->returnNullResponse();
                }
            } else {
                $entity = new $class();

            }
            $this->extraValidate($data, $entity, $extraValidation);
            $entity = $repo->populateEntity($data);
            $this->validate($entity);

            foreach ($arrayPreSave as $callPre) {
                if (is_array($callPre) && key_exists('class', $callPre) && key_exists('method', $callPre)) {
                    call_user_func_array(array($callPre['class'], $callPre['method']), array($extraValidation, &$data, $entity));
                }
            }
            $manager->persist($entity);
            $manager->flush();
            if ($useTransaction)
                $repo->commit();
            foreach ($arrayPostSave as $callPost) {
                if (is_array($callPost) && key_exists('class', $callPost) && key_exists('method', $callPost)) {
                    call_user_func_array(array($callPost['class'], $callPost['method']), array($extraValidation, $data, $entity));
                }
            }
            return array('success' => true, 'data' => array('id' => $entity->getId()));
        } catch (\Exception $exception) {
            //print_r($exception->getMessage());
            // die;
            if ($useTransaction)
                $repo->rollback();
            if (get_class($exception) == 'Doctrine\DBAL\DBALException') {

                $this->resetManager();
            }

            return $this->manageException($exception);
        }
    }

    public function manageException($exception) {

        $return = array('success' => false, 'error' => 'Error desconocido.');
        if($exception instanceof \Symfony\Component\Ldap\Exception\LdapException){
            $return['error']='Verifique la configuración de la extensión del LDAP.';
            return $return;
        }
        if (preg_match('/SQLSTATE\[23505\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('23505');
        } else if (preg_match('/SQLSTATE\[23502\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('23502');
        } /**/ else if (preg_match('/SQLSTATE\[23000\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('23000');
        } else if (preg_match('/SQLSTATE\[HV00N\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('HV00N');
        } else if (preg_match('/SQLSTATE\[28000\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('28000');
        } else if (preg_match('/SQLSTATE\[2F000\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('2F000');
        } else if (preg_match('/SQLSTATE\[53300\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('53300');
        } else if (preg_match('/SQLSTATE\[58000\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('58000');
        } else if (preg_match('/SQLSTATE\[23503\]/', $exception->getMessage())) {
            $return['error'] = $this->get('translator')->trans('23503');
        } else
            $return['error'] = $exception->getMessage();



        return $return;
    }

    public function getRepo($entity) {

        return $this->getManager()->getRepository('AppBundle:' . $entity);
    }

    /*
     */
}
