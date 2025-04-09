<?php
class CourseNotesManager {
    private $sesion;
    
    public function __construct() {
        $this->sesion = &$_SESSION;
    }
    
    // Guardar nota de videotest específica para cada curso
    public function guardarNotaVideotest($nota, $curso) {
        $this->sesion['nota_videotest_' . $curso] = $nota;
    }
    
    // Obtener nota de videotest específica para cada curso
     public function obtenerNotaVideotest($curso) {
        $notas = ['pa' => 'nota_videotest_pa', 'sa' => 'nota_videotest_sa', 'me' => 'nota_videotest_me', 'iperc' => 'nota_videotest_iperc'];
        return isset($this->sesion[$notas[$curso]]) ? 
               $this->sesion[$notas[$curso]] : 
               '--';
    }

    
    // Guardar nota final específica para cada curso
    public function guardarNotaFinal($nota, $estado, $curso) {
        $this->sesion['nota_final_' . $curso] = $nota;
        $this->sesion['estado_' . $curso] = $estado;
    }
    
    // Obtener nota final específica para cada curso
    public function obtenerNotaFinal($curso) {
        return [
            'nota' => isset($this->sesion['nota_final_' . $curso]) ? 
                     $this->sesion['nota_final_' . $curso] : 
                     null,
            'estado' => isset($this->sesion['estado_' . $curso]) ? 
                       $this->sesion['estado_' . $curso] : 
                       null
        ];
    }
    
}
?>