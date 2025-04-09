<?php
// examenes.php - Sistema de exámenes con intentos independientes por curso

session_start();

class SistemaExamenes {
    private $conn;
    private $id_curso;
    private $duracion_examen = 1200; // 20 minutos en segundos
    
    // Constructor
    public function __construct($id_curso) {
        $this->id_curso = $id_curso;
        $this->conectarDB();
        $this->inicializarSesionCurso();
    }
    
    // Conexión a la base de datos
    private function conectarDB() {
        $this->conn = new mysqli('localhost', 'root', '', 'usuario');
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
    }
    
    // Inicializar sesión específica del curso
    private function inicializarSesionCurso() {
        if (!isset($_SESSION['cursos'])) {
            $_SESSION['cursos'] = [];
        }
        
        if (!isset($_SESSION['cursos'][$this->id_curso])) {
            $_SESSION['cursos'][$this->id_curso] = [
                'intentos' => 0,
                'notas' => [],
                'tiempo_inicio' => 0,
                'tiempo_restante' => $this->duracion_examen,
                'examen_iniciado' => false,
                'examen_completado' => false,
                'nota_final' => 0,
                'estado_final' => '',
                'nota_mas_alta' => 0
            ];
        }
    }
    
    // Obtener intentos disponibles
    public function obtenerIntentosDisponibles() {
        return 3 - $_SESSION['cursos'][$this->id_curso]['intentos'];
    }
    
    // Verificar si el examen está completado
    public function examenCompletado() {
        return $_SESSION['cursos'][$this->id_curso]['examen_completado'];
    }
    
    // Iniciar examen
    public function iniciarExamen() {
        if ($this->obtenerIntentosDisponibles() > 0) {
            $_SESSION['cursos'][$this->id_curso]['examen_iniciado'] = true;
            $_SESSION['cursos'][$this->id_curso]['tiempo_inicio'] = time();
            $_SESSION['cursos'][$this->id_curso]['tiempo_restante'] = $this->duracion_examen;
            return true;
        }
        return false;
    }
    
    // Verificar tiempo expirado
    public function tiempoExpirado() {
        $tiempo_transcurrido = time() - $_SESSION['cursos'][$this->id_curso]['tiempo_inicio'];
        return $tiempo_transcurrido >= $_SESSION['cursos'][$this->id_curso]['tiempo_restante'];
    }
    
    // Procesar respuestas
    public function procesarRespuestas($respuestas, $preguntas) {
        $nota = 0;
        
        if ($this->tiempoExpirado()) {
            $this->registrarIntento(0);
            return ['estado' => 'REPROBADO', 'mensaje' => 'Tiempo agotado. Has reprobado el examen.'];
        }
        
        foreach ($respuestas as $index => $respuesta) {
            if ($respuesta == $preguntas[$index][2]) {
                $nota += 4;
            }
        }
        
        return $this->registrarIntento($nota);
    }
    
    // Registrar intento
    private function registrarIntento($nota) {
        $_SESSION['cursos'][$this->id_curso]['notas'][] = $nota;
        $_SESSION['cursos'][$this->id_curso]['examen_iniciado'] = false;
        
        $estado = $nota >= 13 ? 'APROBADO' : 'REPROBADO';
        $_SESSION['cursos'][$this->id_curso]['estado_final'] = $estado;
        
        if ($nota > ($_SESSION['cursos'][$this->id_curso]['nota_mas_alta'] ?? 0)) {
            $_SESSION['cursos'][$this->id_curso]['nota_mas_alta'] = $nota;
        }
        
        // Actualizar en la base de datos
        $this->actualizarNotaEnBD($nota);
        
        $_SESSION['cursos'][$this->id_curso]['intentos']++;
        
        if ($estado == 'APROBADO') {
            $_SESSION['cursos'][$this->id_curso]['examen_completado'] = true;
            return ['estado' => $estado, 'mensaje' => "¡Felicitaciones! Has aprobado el examen con $nota puntos."];
        } else {
            if ($this->obtenerIntentosDisponibles() > 0) {
                return ['estado' => $estado, 'mensaje' => "Has completado el intento " . $_SESSION['cursos'][$this->id_curso]['intentos'] . " con $nota puntos."];
            } else {
                $_SESSION['cursos'][$this->id_curso]['examen_completado'] = true;
                return ['estado' => $estado, 'mensaje' => "Has agotado tus intentos. Nota final: " . $_SESSION['cursos'][$this->id_curso]['nota_mas_alta']];
            }
        }
    }
    
    // Actualizar nota en la base de datos
    private function actualizarNotaEnBD($nota) {
        if (!isset($_SESSION['DNI'])) return;
        
        $DNI = $_SESSION['DNI'];
        $intento_actual = $_SESSION['cursos'][$this->id_curso]['intentos'] + 1;
        $nota_mas_alta = $_SESSION['cursos'][$this->id_curso]['nota_mas_alta'];
        
        $sql = "UPDATE inscripciones SET 
                int{$intento_actual} = ?,
                examen_final = ?
                WHERE DNI = ? AND id_curso = ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ddsi", $nota, $nota_mas_alta, $DNI, $this->id_curso);
        $stmt->execute();
    }
    
    // Reiniciar intentos
    public function reiniciarIntentos() {
        if (!isset($_SESSION['DNI'])) return false;
        
        $DNI = $_SESSION['DNI'];
        $sql = "UPDATE inscripciones SET int1 = 0, int2 = 0, int3 = 0, examen_final = 0 
                WHERE DNI = ? AND id_curso = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $DNI, $this->id_curso);
        
        if ($stmt->execute()) {
            // Reiniciar sesión del curso
            unset($_SESSION['cursos'][$this->id_curso]);
            $this->inicializarSesionCurso();
            return true;
        }
        return false;
    }
    
    // Obtener datos del examen
    public function obtenerDatosExamen() {
        return $_SESSION['cursos'][$this->id_curso];
    }
}

// Ejemplo de uso en cada curso (sa.php, pa.php, me.php):
/*
// En sa.php:
$id_curso = 1; // ID para Soldadura con Arco
$examen = new SistemaExamenes($id_curso);

// En pa.php:
$id_curso = 2; // ID para Proceso Automatizado
$examen = new SistemaExamenes($id_curso);

// En me.php:
$id_curso = 3; // ID para otro curso
$examen = new SistemaExamenes($id_curso);
*/
?>