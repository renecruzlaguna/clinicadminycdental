<?php

namespace AppBundle\Libs\EntityNormalizer;

use AppBundle\Libs\Normalizer\AbstractNormalizer;
use AppBundle\Libs\Decorator\CustomDecorator;


/**
 * Description of TipoVisitaNormalizer
 *
 * @author code TipoVisitaNormalizer
 */
class TipoVisitaNormalizer extends AbstractNormalizer {

    public function normalizeObject($object, $prototype) {

        $obj = array();
        switch ($prototype) {
            case CustomDecorator::DEFAULT_DECORATOR:
                $obj['id'] = $object->getId();
                $obj['nombre'] = $object->getNombre();
                $obj['descripcion'] = $object->getDescripcion();
                break;
        }
        
        return $obj;
    }

}
