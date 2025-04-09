<?php
// Course Page for enfermeria
$course_name = 'enfermeria';
$course_image = 'uploads/67603b9f49b53.jpg';
?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>enfermeria - Curso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .course-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 5px;
        }
        .course-image {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin: 20px 0;
        }
        .course-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .course-section {
            margin-bottom: 20px;
        }
        .course-section h2 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class='course-header'>
        <h1><?= $course_name ?></h1>
    </div>
    
    <?php if (!empty($course_image)): ?>
    <img src='<?= $course_image ?>' alt='Course Image' class='course-image'>
    <?php endif; ?>
    
    <div class='course-content'>
        <div class='course-section'>
            <h2>Material del Curso</h2>
            <p>zzzzzzzzz</p>
        </div>
        
        <div class='course-section'>
            <h2>Evaluación</h2>
            <p>zzzzzzz</p>
        </div>
        
        <div class='course-section'>
            <h2>Examen</h2>
            <p>zzzzzzzz</p>
        </div>
    </div>
</body>
</html>