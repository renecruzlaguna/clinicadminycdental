<?php

namespace AppBundle\Entity;

use ArrayAccess;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Users
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRepository")
 * @UniqueEntity(fields={"nombreUsuario"}, message="Ya existe un usuario con ese nombre")
 * @UniqueEntity(fields={"cedula"}, message="Ya existe un usuario con esa cédula")
 */
class Usuario implements UserInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $edad;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $activo;


    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $salario;
    
      /**
     * @var float
     *
     * @ORM\Column(type="float",options={"default":0},nullable=true)
     */
    private $mensualidad;

    /**
     * @var float
     *
     * @ORM\Column(type="integer",options={"default":0})
     */
    private $tipocliente=0;

    /**
     * @var float
     *
     * @ORM\Column(type="integer",options={"default":0})
     */
    private $porciento=0;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ResultadoConsulta", mappedBy="cliente")
     */
    private $historia;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_usuario", type="string", length=255,unique=true,nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      max = 20,
     *      minMessage = "El nombre de usuario debe tener como mínimo 5 caracteres",
     *      maxMessage = "El nombre de usuario debe tener como máximo 20 caracteres"
     * )
     * @Assert\Regex(
     *     pattern     = "/^([a-z0-9]+)+$/",
     *     message="El nombre de usuario solo debe contener letras o números"
     *
     * )
     */
    private $nombreUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=255)
     *
     * @Assert\Email(
     *     message = "El correo'{{ value }}' no es válido."
     *
     * )
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="cedula", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "La cédula debe tener como mínimo 10 caracteres",
     *      maxMessage = "La cédula debe tener como máximo 10 caracteres"
     * )
     * @Assert\Regex(
     *     pattern     = "/^([0-9]{10,10})+$/",
     *     message="La cédula debe tener el format 9999999999"
     *
     * )
     */
    private $cedula;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern     = "/^([A-Za-záéíóúñÁÉÍÓÚüÜÑ]+\s*)+$/",
     *     message="El nombre solo debe contener letras"
     *
     * )
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern     = "/^([A-Za-záéíóúñÁÉÍÓÚüÜÑ]+\s*)+$/",
     *     message="Los apellidos solo deben contener letras"
     *
     * )
     */
    private $apellido;
    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text")
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="numerohistoria", type="string",length=10,nullable=true,unique=true)
     */
    private $numeroHistoria;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 12,
     *      max = 12,
     *      minMessage = "El teléfono debe tener como mínimo 12 caracteres",
     *      maxMessage = "El teléfono debe tener como máximo 12 caracteres"
     * )
     * @Assert\Regex(
     *     pattern     = "/^([0-9]{2,2}-[0-9]{4,4}-[0-9]{4,4})+$/",
     *     message="El teléfono  debe tener el formato 99-9999-9999"
     *
     * )
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="telefonoc", type="string",length=10,nullable=true)
     */
    private $telefonoConvencional;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rol", inversedBy="usuario")
     * @Assert\NotNull( message="Debe de seleccionar un rol")
     * @ORM\JoinColumn(
     *     name="id_rol",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $rol;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Especialidad", inversedBy="usuario")
     * @ORM\JoinColumn(
     *     name="id_especialidad",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    private $especialidad;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Consulta", mappedBy="usuario")
     */
    private $consulta;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Imagen", mappedBy="usuario")
     */
    private $imagen;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Consulta", mappedBy="paciente")
     */
    private $registroDentalPaciente;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Consulta", mappedBy="medico")
     */
    private $registroDentalMedico;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Consulta", mappedBy="usuarioRegistro")
     */
    private $consultaRegistro;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlanTrabajo", mappedBy="usuario")
     */
    private $planTrabajo;


    public function eraseCredentials()
    {

    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array($this->rol->getNombre());

    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->nombreUsuario;
    }

    public function getCompleteName()
    {
        $array = array();
        $array[] = $this->getNombre();
        $array[] = $this->getApellido();
        return implode(' ', $array);
    }



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombreUsuario
     *
     * @param string $nombreUsuario
     * @return Usuario
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    /**
     * Get nombreUsuario
     *
     * @return string
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * Set correo
     *
     * @param string $correo
     * @return Usuario
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Usuario
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set rol
     *
     * @param \AppBundle\Entity\Rol $rol
     * @return Usuario
     */
    public function setRol(\AppBundle\Entity\Rol $rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return \AppBundle\Entity\Rol
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Add consulta
     *
     * @param \AppBundle\Entity\Consulta $consulta
     * @return Usuario
     */
    public function addConsultum(\AppBundle\Entity\Consulta $consulta)
    {
        $this->consulta[] = $consulta;

        return $this;
    }

    /**
     * Remove consulta
     *
     * @param \AppBundle\Entity\Consulta $consulta
     */
    public function removeConsultum(\AppBundle\Entity\Consulta $consulta)
    {
        $this->consulta->removeElement($consulta);
    }

    /**
     * Get consulta
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsulta()
    {
        return $this->consulta;
    }

    /**
     * Add planTrabajo
     *
     * @param \AppBundle\Entity\PlanTrabajo $planTrabajo
     * @return Usuario
     */
    public function addPlanTrabajo(\AppBundle\Entity\PlanTrabajo $planTrabajo)
    {
        $this->planTrabajo[] = $planTrabajo;

        return $this;
    }

    /**
     * Remove planTrabajo
     *
     * @param \AppBundle\Entity\PlanTrabajo $planTrabajo
     */
    public function removePlanTrabajo(\AppBundle\Entity\PlanTrabajo $planTrabajo)
    {
        $this->planTrabajo->removeElement($planTrabajo);
    }

    /**
     * Get planTrabajo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlanTrabajo()
    {
        return $this->planTrabajo;
    }

    /**
     * Set cedula
     *
     * @param string $cedula
     * @return Usuario
     */
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;

        return $this;
    }

    /**
     * Get cedula
     *
     * @return string
     */
    public function getCedula()
    {
        return $this->cedula;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        if (!empty($password)) {

            $encode = new MessageDigestPasswordEncoder(
                'sha512', false, 10
            );

            $this->password = $encode->encodePassword($password, '');
        }
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Usuario
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Usuario
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set especialidad
     *
     * @param \AppBundle\Entity\Especialidad $especialidad
     * @return Usuario
     */
    public function setEspecialidad(\AppBundle\Entity\Especialidad $especialidad = null)
    {
        $this->especialidad = $especialidad;

        return $this;
    }

    /**
     * Get especialidad
     *
     * @return \AppBundle\Entity\Especialidad
     */
    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    public function toJson()
    {
        return array('nombrecompleto' => $this->getCompleteName(), 'id' => $this->id);
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        /*para que en un ca;back ponga el path del donde es el error*/
        if ($this->telefonoConvencional) {
            $notNull = new Assert\Regex(array('pattern' => "/^([0-9]{2,2}-[0-9]{7,7})+$/", 'message' => "El teléfono  debe tener el formato 99-9999999."));
            $context->validateValue($this->telefonoConvencional, $notNull, 'telefonoConvencional');
        }
        if ($this->rol->getId() == 2) {
            $notNull = new Assert\NotNull(array('message' => "Debe de seleccionar una especialidad."));
            $context->validateValue($this->especialidad, $notNull, 'especialidad');

            $notNull = new Assert\GreaterThanOrEqual(array('message' => "El salario debe ser mayor o igual 0", 'value' => 0));
            $context->validateValue($this->salario, $notNull, 'salario');
        }
        if ($this->rol->getId() == 1) {
            $notNull = new Assert\Range(array('maxMessage' => "La edad debe de ser menor que 120", 'minMessage' => "La edad debe de ser mayor que 0", 'min' => 1, 'max' => 120));
            $context->validateValue($this->edad, $notNull, 'edad');
        }
    }

    /**
     * Set edad
     *
     * @param integer $edad
     * @return Usuario
     */
    public function setEdad($edad)
    {
        $this->edad = $edad;

        return $this;
    }

    /**
     * Get edad
     *
     * @return integer
     */
    public function getEdad()
    {
        return $this->edad;
    }

    /**
     * Set salario
     *
     * @param float $salario
     * @return Usuario
     */
    public function setSalario($salario)
    {
        $this->salario = $salario;

        return $this;
    }

    /**
     * Get salario
     *
     * @return float
     */
    public function getSalario()
    {
        return $this->salario;
    }

    /**
     * Add consultaRegistro
     *
     * @param \AppBundle\Entity\Consulta $consultaRegistro
     * @return Usuario
     */
    public function addConsultaRegistro(\AppBundle\Entity\Consulta $consultaRegistro)
    {
        $this->consultaRegistro[] = $consultaRegistro;

        return $this;
    }

    /**
     * Remove consultaRegistro
     *
     * @param \AppBundle\Entity\Consulta $consultaRegistro
     */
    public function removeConsultaRegistro(\AppBundle\Entity\Consulta $consultaRegistro)
    {
        $this->consultaRegistro->removeElement($consultaRegistro);
    }

    /**
     * Get consultaRegistro
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsultaRegistro()
    {
        return $this->consultaRegistro;
    }

    /**
     * Set activo
     *
     * @param integer $activo
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return integer
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Add registroDentalPaciente
     *
     * @param \AppBundle\Entity\Consulta $registroDentalPaciente
     * @return Usuario
     */
    public function addRegistroDentalPaciente(\AppBundle\Entity\Consulta $registroDentalPaciente)
    {
        $this->registroDentalPaciente[] = $registroDentalPaciente;

        return $this;
    }

    /**
     * Remove registroDentalPaciente
     *
     * @param \AppBundle\Entity\Consulta $registroDentalPaciente
     */
    public function removeRegistroDentalPaciente(\AppBundle\Entity\Consulta $registroDentalPaciente)
    {
        $this->registroDentalPaciente->removeElement($registroDentalPaciente);
    }

    /**
     * Get registroDentalPaciente
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroDentalPaciente()
    {
        return $this->registroDentalPaciente;
    }

    /**
     * Add registroDentalMedico
     *
     * @param \AppBundle\Entity\Consulta $registroDentalMedico
     * @return Usuario
     */
    public function addRegistroDentalMedico(\AppBundle\Entity\Consulta $registroDentalMedico)
    {
        $this->registroDentalMedico[] = $registroDentalMedico;

        return $this;
    }

    /**
     * Remove registroDentalMedico
     *
     * @param \AppBundle\Entity\Consulta $registroDentalMedico
     */
    public function removeRegistroDentalMedico(\AppBundle\Entity\Consulta $registroDentalMedico)
    {
        $this->registroDentalMedico->removeElement($registroDentalMedico);
    }

    /**
     * Get registroDentalMedico
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroDentalMedico()
    {
        return $this->registroDentalMedico;
    }

    /**
     * Set historia
     *
     * @param \AppBundle\Entity\ResultadoConsulta $historia
     * @return Usuario
     */
    public function setHistoria(\AppBundle\Entity\ResultadoConsulta $historia = null)
    {
        $this->historia = $historia;

        return $this;
    }

    /**
     * Get historia
     *
     * @return \AppBundle\Entity\ResultadoConsulta
     */
    public function getHistoria()
    {
        return $this->historia;
    }

    /**
     * Set telefonoConvencional
     *
     * @param string $telefonoConvencional
     * @return Usuario
     */
    public function setTelefonoConvencional($telefonoConvencional)
    {
        $this->telefonoConvencional = $telefonoConvencional;

        return $this;
    }

    /**
     * Get telefonoConvencional
     *
     * @return string 
     */
    public function getTelefonoConvencional()
    {
        return $this->telefonoConvencional;
    }

    /**
     * Set tipocliente
     *
     * @param integer $tipocliente
     * @return Usuario
     */
    public function setTipocliente($tipocliente)
    {
        $this->tipocliente = $tipocliente;

        return $this;
    }

    /**
     * Get tipocliente
     *
     * @return integer 
     */
    public function getTipocliente()
    {
        return $this->tipocliente;
    }

    /**
     * Set porciento
     *
     * @param integer $porciento
     * @return Usuario
     */
    public function setPorciento($porciento)
    {
        $this->porciento = $porciento;

        return $this;
    }

    /**
     * Get porciento
     *
     * @return integer 
     */
    public function getPorciento()
    {
        return $this->porciento;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->consulta = new \Doctrine\Common\Collections\ArrayCollection();
        $this->imagen = new \Doctrine\Common\Collections\ArrayCollection();
        $this->registroDentalPaciente = new \Doctrine\Common\Collections\ArrayCollection();
        $this->registroDentalMedico = new \Doctrine\Common\Collections\ArrayCollection();
        $this->consultaRegistro = new \Doctrine\Common\Collections\ArrayCollection();
        $this->planTrabajo = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add imagen
     *
     * @param \AppBundle\Entity\Imagen $imagen
     * @return Usuario
     */
    public function addImagen(\AppBundle\Entity\Imagen $imagen)
    {
        $this->imagen[] = $imagen;

        return $this;
    }

    /**
     * Remove imagen
     *
     * @param \AppBundle\Entity\Imagen $imagen
     */
    public function removeImagen(\AppBundle\Entity\Imagen $imagen)
    {
        $this->imagen->removeElement($imagen);
    }

    /**
     * Get imagen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set numeroHistoria
     *
     * @param string $numeroHistoria
     * @return Usuario
     */
    public function setNumeroHistoria($numeroHistoria)
    {
        $this->numeroHistoria = $numeroHistoria;

        return $this;
    }

    /**
     * Get numeroHistoria
     *
     * @return string 
     */
    public function getNumeroHistoria()
    {
        return $this->numeroHistoria;
    }

    /**
     * Set mensualidad
     *
     * @param float $mensualidad
     * @return Usuario
     */
    public function setMensualidad($mensualidad)
    {
        $this->mensualidad = $mensualidad;

        return $this;
    }

    /**
     * Get mensualidad
     *
     * @return float 
     */
    public function getMensualidad()
    {
        return $this->mensualidad;
    }
}
