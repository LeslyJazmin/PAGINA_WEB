<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="calendario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <nav>
                <h2>MEKADDESH SOLUTION E.I.R.L</h2>
                <ul>
                    <li><a href="inicio_admin.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="cursosa.php"><i class="fas fa-book"></i> Cursos</a></li>
                    <li><a href="auladmi.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="calendarioadmi.php"class="selected"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </aside>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Aplicar el zoom del 90% para todas las pantallas
                document.body.style.zoom = "90%";
                
                // Detectar si la pantalla es más pequeña que 480px de ancho
                if (window.matchMedia("(max-width: 480px)").matches) {
                document.body.style.zoom = "55%"; // Aplicar 50% de zoom en pantallas pequeñas
                }
            });
            
            // Agregar un listener para cambiar el zoom si el usuario redimensiona la pantalla
            window.addEventListener('resize', function() {
                if (window.matchMedia("(max-width: 480px)").matches) {
                document.body.style.zoom = "55%"; // Aplicar 50% de zoom en pantallas pequeñas
                } else {
                document.body.style.zoom = "90%"; // Restaurar a 90% en pantallas más grandes
                }
            });
            </script>
            <style>
                    body {
                        zoom: 90%; /* Aplica un zoom del 75% a todo el contenido */
                    }
          </style>
            <script>
            function confirmLogout() {
                return confirm("¿Estás seguro de que deseas cerrar sesión?");
            }
            </script>
         <!-- Calendar and reminders -->
         <div class="content">
            <div class="calendar">
                <div class="month">
                    <button id="prevMonth">Anterior</button>
                    <div id="monthYear"></div>
                    <button id="nextMonth">Siguiente</button>
                </div>
                <div class="days"></div>
            </div>

            <!-- Reminder List and Modal -->
            <button class="show-reminders-button" onclick="toggleReminderList()">Mostrar Recordatorios</button>
            <div class="reminder-list" id="reminderList" style="display: none;"></div>
        </div>

        <div class="modal" id="reminderModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Agregar Recordatorio</h2>
                </div>
                <div class="modal-body">
                    <input type="text" id="reminderText" placeholder="Escribe tu recordatorio aquí...">
                </div>
                <div class="modal-footer">
                    <button class="cancel" onclick="hideModal()">Cancelar</button>
                    <button class="save" onclick="saveReminder()">Guardar</button>
                </div>
            </div>
        </div>

        <!-- Button to open the modal -->
        <button class="save-reminder-button" onclick="showModal()">Agregar Recordatorio</button>
    </div>

    <!-- JavaScript -->
    <script>
        const monthYearElement = document.getElementById('monthYear');
        const daysElement = document.querySelector('.days');
        const reminderModal = document.getElementById('reminderModal');
        const reminderText = document.getElementById('reminderText');
        const reminderListElement = document.getElementById('reminderList');

        let currentDate = new Date();
        let selectedDate = null;
        let reminders = {};

        // Load calendar and reminders
        document.addEventListener("DOMContentLoaded", function() {
            loadReminders();
            renderCalendar(currentDate);
        });

        function renderCalendar(date) {
            const year = date.getFullYear();
            const month = date.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);

            monthYearElement.textContent = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase();

            daysElement.innerHTML = '';
            for (let i = 1; i < firstDay.getDay(); i++) {
                const emptyDiv = document.createElement('div');
                daysElement.appendChild(emptyDiv);
            }

            for (let i = 1; i <= lastDay.getDate(); i++) {
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.textContent = i;
                dayDiv.onclick = () => selectDate(new Date(year, month, i));

                if (isToday(new Date(year, month, i))) {
                    dayDiv.classList.add('today');
                }

                daysElement.appendChild(dayDiv);
            }
        }

        function isToday(date) {
            const today = new Date();
            return date.getDate() === today.getDate() &&
                   date.getMonth() === today.getMonth() &&
                   date.getFullYear() === today.getFullYear();
        }

        function selectDate(date) {
            selectedDate = date;
            showModal();
        }

        function showModal() {
            reminderText.value = '';
            reminderModal.style.display = 'flex';
        }

        function hideModal() {
            reminderModal.style.display = 'none';
        }

        function saveReminder() {
            if (selectedDate) {
                reminders[selectedDate.toDateString()] = reminderText.value;
                localStorage.setItem('reminders', JSON.stringify(reminders)); // Save in localStorage
                updateReminderList();
            }
            hideModal();
        }

        function deleteReminder(date) {
            delete reminders[date];
            localStorage.setItem('reminders', JSON.stringify(reminders)); // Save changes in localStorage
        }

        function updateReminderList() {
            reminderListElement.innerHTML = ''; // Limpiar la lista de recordatorios
            for (const [date, reminder] of Object.entries(reminders)) {
                const listItem = document.createElement('div');
                listItem.className = 'reminder-item'; // Añadir clase para estilos
                listItem.textContent = `${date}: ${reminder}`;
                
                // Crear botón de eliminar
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Eliminar';
                deleteButton.className = 'delete-button';
                deleteButton.onclick = () => {
                    deleteReminder(date); // Eliminar el recordatorio
                    listItem.remove(); // Eliminar el contenedor del recordatorio
                };

                listItem.appendChild(deleteButton);
                reminderListElement.appendChild(listItem);
            }
        }

        function loadReminders() {
            const savedReminders = localStorage.getItem('reminders');
            if (savedReminders) {
                reminders = JSON.parse(savedReminders);
                updateReminderList();
            }
        }

        function toggleReminderList() {
            const isHidden = reminderListElement.style.display === 'none';
            reminderListElement.style.display = isHidden ? 'block' : 'none';
        }

        document.getElementById('prevMonth').onclick = () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        };

        document.getElementById('nextMonth').onclick = () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        };
    </script>
</body>
</html>