<?php

require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Appointment.php';

class DoctorController
{
    private $doctorModel = null;
    private $appointmentModel = null;

    /*
     * No conectamos ni creamos modelos aquí.
     * Cada modelo se crea cuando una acción realmente lo necesita.
     */
    public function __construct()
    {
    }

    private function doctorModel(): Doctor
    {
        if ($this->doctorModel === null) {
            $this->doctorModel = new Doctor();
        }

        return $this->doctorModel;
    }

    private function appointmentModel(): Appointment
    {
        if ($this->appointmentModel === null) {
            $this->appointmentModel = new Appointment();
        }

        return $this->appointmentModel;
    }

    public function createDoctor()
    {
        $doctor = new Doctor();
        $doctor->correo = htmlspecialchars(strip_tags($_POST['correo']));
        $doctor->contrasena = password_hash(
            htmlspecialchars(strip_tags($_POST['contrasena'])),
            PASSWORD_BCRYPT
        );
        $doctor->nombres = htmlspecialchars(strip_tags($_POST['nombres']));
        $doctor->apellidos = htmlspecialchars(strip_tags($_POST['apellidos']));
        $doctor->especialidad = htmlspecialchars(strip_tags($_POST['especialidad']));
        $doctor->telefono = htmlspecialchars(strip_tags($_POST['telefono']));
        $doctor->horario = htmlspecialchars(strip_tags($_POST['horario']));

        if ($doctor->create()) {
            return [
                "success" => true,
                "message" => "Doctor created successfully."
            ];
        }

        return [
            "success" => false,
            "message" => "Doctor could not be created."
        ];
    }

    public function getDoctors()
    {
        return $this->doctorModel()->read();
    }

    public function loginDoctor()
    {
        $message = '';

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['correo'], $_POST['contrasena'])
        ) {
            $doctor = new Doctor();
            $doctor->correo = htmlspecialchars(strip_tags($_POST['correo']));
            $doctor->contrasena = $_POST['contrasena'];

            $result = $doctor->login();

            if ($result) {
                if (password_verify($doctor->contrasena, $result['contrasena'])) {
                    session_start();

                    $_SESSION['doctor_id'] = $result['doctor_id'];
                    $_SESSION['doctor_name'] = $result['nombres'];

                    header("Location: ../views/doctor/dashboard.php");
                    exit();
                }

                $message = "Contraseña incorrecta";
            } else {
                $message = "Correo electrónico incorrecto";
            }
        }

        // Conservar la vista y el flujo existentes.
        include '../views/doctor/login.php';
    }

    public function dashboard()
    {
        if (!isset($_SESSION['doctor_id'])) {
            return [
                "success" => false,
                "message" => "Not logged in."
            ];
        }

        $doctorId = $_SESSION['doctor_id'];

        $appointments = $this->appointmentModel()
            ->getDoctorAppointments($doctorId);

        return [
            "success" => true,
            "appointments" => $appointments
        ];
    }

    public function updateCalendar()
    {
        if (!isset($_SESSION['doctor_id'])) {
            return [
                "success" => false,
                "message" => "Not logged in."
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $calendarData = $_POST['calendar_data'];

            if ($this->doctorModel()->updateCalendar($calendarData)) {
                return [
                    "success" => true,
                    "message" => "Calendar updated successfully."
                ];
            }

            return [
                "success" => false,
                "message" => "Calendar could not be updated."
            ];
        }

        return [
            "success" => false,
            "message" => "Invalid request method."
        ];
    }

    public function getAppointmentById($appointmentId)
    {
        return $this->appointmentModel()
            ->getAppointmentByIddd($appointmentId);
    }

    public function getAppointmentsByDoctorId($doctorId)
    {
        return $this->appointmentModel()
            ->getDoctorAppointments($doctorId);
    }

    public function getPatientsByDoctorId(int $doctorId)
    {
        return $this->doctorModel()
            ->getPatientsForDoctor($doctorId);
    }

    public function getPatients()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['doctor_id'])) {
            http_response_code(401);
            return [];
        }

        return $this->getPatientsByDoctorId(
            (int) $_SESSION['doctor_id']
        );
    }

    public function updateAppointmentForDoctor(
        int $appointmentId,
        int $doctorId,
        array $data
    ): bool {
        return $this->appointmentModel()
            ->updateForDoctor($appointmentId, $doctorId, $data);
    }

    public function cancelAppointmentForDoctor(
        int $appointmentId,
        int $doctorId
    ): bool {
        return $this->appointmentModel()
            ->cancelForDoctor($appointmentId, $doctorId);
    }

    public function updateAppointmentStatus(
        $appointmentId,
        $status,
        $doctorId
    ) {
        $appointment = $this->appointmentModel()
            ->getAppointmentById($appointmentId);

        if (
            !$appointment
            || (int) $appointment['doctor_id'] !== (int) $doctorId
        ) {
            return false;
        }

        return $this->appointmentModel()->updateForDoctor(
            (int) $appointmentId,
            (int) $doctorId,
            [
                'dni' => $appointment['dni'],
                'fecha_nacimiento' => $appointment['fecha_nacimiento'],
                'sexo' => $appointment['sexo'],
                'direccion' => $appointment['direccion'],
                'telefono' => $appointment['telefono'],
                'especialidad' => $appointment['especialidad'],
                'estado' => $status,
            ]
        );
    }

    public function getDoctorById($doctorId)
    {
        return $this->doctorModel()->getDoctorById($doctorId);
    }

    public function createAppointment($appointmentData)
    {
        $appointment = $this->appointmentModel();

        $appointment->dni = $appointmentData['dni'];
        $appointment->fecha_nacimiento = $appointmentData['fecha_nacimiento'];
        $appointment->sexo = $appointmentData['sexo'];
        $appointment->direccion = $appointmentData['direccion'];
        $appointment->telefono = $appointmentData['telefono'];
        $appointment->especialidad = $appointmentData['especialidad'];
        $appointment->usuario_id = $appointmentData['usuario_id'];
        $appointment->doctor_id = $appointmentData['doctor_id'];

        if ($appointment->create()) {
            return [
                "success" => true,
                "message" => "Appointment created successfully."
            ];
        }

        return [
            "success" => false,
            "message" => "Failed to create appointment."
        ];
    }

    public function updateAppointment($appointmentData)
    {
        $appointment = $this->appointmentModel();

        $appointment->cita_id = $appointmentData['cita_id'];
        $appointment->dni = $appointmentData['dni'];
        $appointment->fecha_nacimiento = $appointmentData['fecha_nacimiento'];
        $appointment->sexo = $appointmentData['sexo'];
        $appointment->direccion = $appointmentData['direccion'];
        $appointment->telefono = $appointmentData['telefono'];
        $appointment->especialidad = $appointmentData['especialidad'];
        $appointment->doctor_id = $appointmentData['doctor_id'];

        if ($appointment->update()) {
            return [
                "success" => true,
                "message" => "Appointment updated successfully."
            ];
        }

        return [
            "success" => false,
            "message" => "Failed to update appointment."
        ];
    }

    public function deleteAppointment($appointmentId)
    {
        if ($this->appointmentModel()->delete($appointmentId)) {
            return [
                "success" => true,
                "message" => "Appointment deleted successfully."
            ];
        }

        return [
            "success" => false,
            "message" => "Failed to delete appointment."
        ];
    }

    public function addPatient()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = $_POST['dni'];
            $nombre = $_POST['nombre'];
            $fechaNacimiento = $_POST['fecha_nacimiento'];
            $sexo = $_POST['sexo'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $especialidad = $_POST['especialidad'];

            $appointment = new Appointment();

            $appointment->dni = $dni;
            $appointment->nombre = $nombre;
            $appointment->fecha_nacimiento = $fechaNacimiento;
            $appointment->sexo = $sexo;
            $appointment->direccion = $direccion;
            $appointment->telefono = $telefono;
            $appointment->especialidad = $especialidad;
            $appointment->insertar_nombre = $nombre;

            if ($appointment->create()) {
                echo "Paciente agregado con éxito.";
            } else {
                echo "Error al agregar paciente.";
            }
        } else {
            // Conservar el formulario y la ruta existentes.
            include 'views/users/add_patient.php';
        }
    }
}
