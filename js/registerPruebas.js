$(document).ready(function() {
    var form = $('.creacuentaRegister');

    form.append('<div class="datosUsuarioRegister">' +
                '<input class="inputRegisterPHP" type="text" id="username" name="username" required>' +
                '<label for="username">Usuario</label>' +
            '</div>');

    // Manejar el evento keydown en el campo de entrada del nombre de usuario
    $('#username').keydown(function(e) {
        // Si la tecla presionada es Enter (keyCode 13) o Tab (keyCode 9)
        if (e.keyCode == 13 || e.keyCode == 9) {
            // Prevenir la acción por defecto (enviar el formulario o moverse al siguiente campo)
            e.preventDefault();

            // Obtener el valor del campo de entrada
            var username = $(this).val();

            // Validar que el nombre de usuario tenga al menos 5 caracteres y contenga mayúsculas y minúsculas
            if (username.length >= 5 && username != username.toLowerCase() && username != username.toUpperCase()) {
                // Si la validación es exitosa, agregar el campo de entrada del correo electrónico
                form.append('<div class="datosUsuarioRegister">' +
                            '<input class="inputRegisterPHP" type="email" id="email" name="email" required>' +
                            '<label for="email">Correo electrónico</label>' +
                        '</div>');

                // Mover el foco al campo de entrada del correo electrónico
                $('#email').focus();

                // Manejar el evento keydown en el campo de entrada del correo electrónico
                $('#email').keydown(function(e) {
                    // Si la tecla presionada es Enter (keyCode 13) o Tab (keyCode 9)
                    if (e.keyCode == 13 || e.keyCode == 9) {
                        // Prevenir la acción por defecto (enviar el formulario o moverse al siguiente campo)
                        e.preventDefault();

                        // Obtener el valor del campo de entrada
                        var email = $(this).val();

                        // Validar que el correo electrónico tenga el formato correcto
                        var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                        if (emailRegex.test(email)) {
                            // Si la validación es exitosa, agregar el campo de entrada de la contraseña
                            form.append('<div class="datosUsuarioRegister">' +
                                        '<input class="inputRegisterPHP" type="password" id="password" name="password" required>' +
                                        '<label for="password">Contraseña</label>' +
                                    '</div>');

                            // Mover el foco al campo de entrada de la contraseña
                            $('#password').focus();

                            // Manejar el evento keydown en el campo de entrada de la contraseña
                            $('#password').keydown(function(e) {
                                if (e.keyCode == 13 || e.keyCode == 9) {
                                    e.preventDefault();
                                    var password = $(this).val();
                                    if (password.length < 8) {
                                        showErrorPopup('La contraseña debe tener un mínimo de 8 carácteres.');
                                    } else if (!/[0-9]/.test(password)) {
                                        showErrorPopup('La contraseña debe contener al menos un carácter numérico.');
                                    } else if (!/[A-Z]/.test(password)) {
                                        showErrorPopup('La contraseña debe contener al menos una mayúscula.');
                                    } else if (!/[a-z]/.test(password)) {
                                        showErrorPopup('La contraseña debe contener al menos una minúscula.');
                                    } else if (!/[!@#$%^&*]/.test(password)) {
                                        showErrorPopup('La contraseña debe contener al menos un carácter especial.');
                                    } else {
                                        form.append('<div class="datosUsuarioRegister">' +
                                                    '<input class="inputRegisterPHP" type="password" id="confirmPassword" name="confirm_password" required>' +
                                                    '<label for="confirmPassword">Confirmar contraseña</label>' +
                                                '</div>');
                                                setTimeout(function() {
                                                    $('#confirmPassword').focus();
                                                }, 0);
                            
                                                $('#confirmPassword').keydown(function(e) {
                                                    if (e.keyCode == 13 || e.keyCode == 9) {
                                                        e.preventDefault();
                                                        var confirmPassword = $(this).val();
                                                        if (confirmPassword !== password) {
                                                            showErrorPopup('Las contraseñas no coinciden.');
                                                        } else {
                                                            // Si la contraseña confirmada es la misma que la contraseña original, agrega el campo de selección de país
                                                            form.append(countrySelectHTML);
                                                            setTimeout(function() {
                                                                $('#country').focus();
                                                            }, 0);$('#country').change(function() {
                                                                // Agrega el campo de número de teléfono
                                                                form.append('<div class="datosUsuarioRegister">' +
                                                                            '<input class="inputRegisterPHP" type="tel" id="telephone" name="telephone" required>' +
                                                                            '<label for="telephone">Número de teléfono</label>' +
                                                                        '</div>');
                                                
                                                                // Obtiene el prefijo del país seleccionado
                                                                var selectedCountryPrefix = $('#country option:selected').data('prefix');
                                                
                                                                // Establece el valor del campo de número de teléfono al prefijo del país
                                                                $('#telephone').val(selectedCountryPrefix);
                                                
                                                                setTimeout(function() {
                                                                    $('#telephone').focus();
                                                                }, 0);
                                                            });
                                                        }
                                                    }
                                                });
                                    }
                                }
                            });
                        } else {
                            // Si la validación falla, mostrar un mensaje de error
                            alert('Por favor, introduce un correo electrónico válido.');
                        }
                    }
                });
            } else {
                // Si la validación falla, mostrar un mensaje de error
                alert('El nombre de usuario debe tener al menos 5 caracteres y contener mayúsculas y minúsculas.');
            }
        }
    });
});


function showErrorPopup(message) {
    // Crear la ventana flotante
    var errorPopup = $('<div/>', {
        id: 'errorPopup',
        text: message,
        style: 'position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); background-color: #f44336; color: white; padding: 20px; border-radius: 5px;'
    });
  
    // Crear el botón "X"
    var closeButton = $('<button/>', {
        text: 'X',
        style: 'position: absolute; top: 0; right: 0; background-color: transparent; color: white; border: none; font-size: 20px; cursor: pointer;'
    });
  
    // Añadir el botón "X" a la ventana flotante
    errorPopup.append(closeButton);
  
    // Añadir la ventana flotante al cuerpo del documento
    $('body').append(errorPopup);
  
    // Manejador de eventos para el botón "X"
    closeButton.click(function() {
        errorPopup.remove();
    });
  }