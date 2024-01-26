$(document).ready(function() {
    var form = $('.creacuentaRegister');

    form.append('<div class="datosUsuarioRegister">' +
                '<input class="inputRegisterPHP" type="text" id="username" name="username" required>' +
                '<label for="username">Usuario</label>' +
            '</div>');

    // Manejar el evento keydown en el campo de entrada del nombre de usuario
    form.on('keydown', '#username', function(e) {
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
                form.on('keydown', '#email', function(e) {
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
                            form.on('keydown', '#password', function(e) {
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
                            
                                                form.on('keydown', '#confirmPassword', function(e) {

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
                                                            }, 0);
                                                            $('#country').change(function() {
                                                                // Agrega el campo de número de teléfono
                                                                form.append('<div class="datosUsuarioRegister">' +
                                                                            '<div style="display: flex; justify-content: space-between;">' +
                                                                            '<input class="inputRegisterPHP" type="tel" id="countryPrefix" name="countryPrefix" readonly style="margin-right: 10px; width: 15%;">' +
                                                                            '<input class="inputRegisterPHP" type="tel" id="telephone" name="telephone" required style="width: 80%;" placeholder="Número de teléfono">' +
                                                                            '</div>' +
                                                                            '</div>');
                                                
                                                                // Obtiene el prefijo del país seleccionado
                                                                var selectedCountryPrefix = $('#country option:selected').data('prefix');
                                                
                                                                // Establece el valor del campo de número de teléfono al prefijo del país
                                                                $('#countryPrefix').val(selectedCountryPrefix);
                                                
                                                                setTimeout(function() {
                                                                    $('#telephone').focus();
                                                                }, 0);
                                                                form.on('keyup', '#telephone', function(e) {
                                                                    var telephone = $(this).val();
                                                                    var prefix = $('#countryPrefix').val();
                                                                    var telephoneWithoutPrefix = telephone.replace(prefix, '');
                                                                
                                                                    // If the Enter or Tab key is pressed
                                                                    if (e.keyCode === 13 || e.keyCode === 9) {
                                                                        // Check if the phone number has a length of 9
                                                                        if (telephoneWithoutPrefix.length !== 9) {
                                                                            showErrorPopup('Longitud del numero de teléfono incorrecta.');
                                                                            return;
                                                                        }
                                                                
                                                                        // Check if the phone number contains only digits
                                                                        if (!/^\d+$/.test(telephoneWithoutPrefix)) {
                                                                            showErrorPopup('El número de teléfono no debe contener caracteres no permitidos.');
                                                                            return;
                                                                        }
                                                                
                                                                        // If the phone number is valid, add the city input field
                                                                        if (telephoneWithoutPrefix.length === 9 && /^\d+$/.test(telephoneWithoutPrefix)) {
                                                                            form.append('<div class="datosUsuarioRegister">' +
                                                                                        '<input class="inputRegisterPHP" type="text" id="city" name="city" required>' +
                                                                                        '<label for="city">Ciudad</label>' +
                                                                                        '</div>');
                                                                        
                                                                            // Aquí es donde actualizas el valor del campo de teléfono en tu formulario HTML
                                                                            // para que sea el número de teléfono completo (incluyendo el prefijo).
                                                                            $('#telephone').val(telephone);
                                                                        }
                                                                    }
                                                                });
                                                                
                                                                form.on('keyup', '#city', function(e) {

                                                                    // Check if the Enter or Tab key is pressed
                                                                    if (e.keyCode === 13 || e.keyCode === 9) {
                                                                        var city = $(this).val();
                                                                
                                                                        // Check if the city field is not empty
                                                                        if (city !== '') {
                                                                            // Add the zipcode input field
                                                                            form.append('<div class="datosUsuarioRegister">' +
                                                                                        '<input class="inputRegisterPHP" type="text" pattern="[0-9]{5}" id="zipcode" name="zipcode" required>' +
                                                                                        '<label for="zipcode">Código postal</label>' +
                                                                                        '</div>');
                                                                        } else {
                                                                            // Show an error message
                                                                            showErrorPopup('Por favor, introduce una ciudad.');
                                                                        }
                                                                    }
                                                                });
                                                                form.on('keyup', '#zipcode', function(e) {
                                                                    // Check if the Enter or Tab key is pressed
                                                                    if (e.keyCode === 13 || e.keyCode === 9) {
                                                                        var zipcode = $(this).val();
                                                                
                                                                        // Check if the zipcode field contains exactly 5 digits
                                                                        if (/^\d{5}$/.test(zipcode)) {
                                                                            // The zipcode is valid, append the register button
                                                                            form.append('<button id="siguienteBotonRegister" type="submit">Registrar</button>');
                                                                        } else {
                                                                            // Show an error message
                                                                            showErrorPopup('Por favor, introduce un código postal válido.');
                                                                        }
                                                                    }
                                                                });


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


     // Cuando el campo de username está vacío, borra los campos de abajo y agrega el botón Siguiente para el username
     form.on('input', '#username', function() {
        if (!$(this).val()) {
            $('#username').val('');
            
            //$('#siguienteBotonRegisterUsername').remove(); // Elimina el botón Siguiente para el username
            $('#email').val('');
            $('#email').parent().remove(); // Elimina el campo de email
           // $('#siguienteBotonRegisterEmail').remove(); // Elimina el botón Siguiente para el email
            $('#password').val('');
            $('#password').parent().remove(); // Elimina el campo de password
           // $('#siguienteBotonRegisterPassword').remove(); // Elimina el botón Siguiente para el password
            $('#confirmPassword').val('');
            $('#confirmPassword').parent().remove(); // Elimina el campo de repetir password
           // $('#siguienteBotonRegisterConfirmPassword').remove(); // Elimina el botón Siguiente del campo repetir password
            $('#telephone').val('');
            $('#telephone').parent().remove(); // Elimina el campo de tlf
           // $('#siguienteBotonRegisterTelephone').remove(); // Elimina el botón Siguiente del campo tlf
            $('#country').val('');
            $('#country').parent().remove(); // Elimina el campo de PAIS
           // $('#siguienteBotonRegisterCountry').remove(); // Elimina el botón Siguiente del campo PAIS
            $('#city').val('');
            $('#city').parent().remove(); // Elimina el campo de city
           // $('#siguienteBotonRegisterCity').remove(); // Elimina el botón Siguiente del campo city
            $('#zipcode').val('');
            $('#zipcode').parent().remove(); // Elimina el campo de codigo postal
           // $('#siguienteBotonRegister').remove(); // Elimina el botón Siguiente del campo REGISTRARSE
           // form.append('<button id="siguienteBotonRegisterUsername" type="button">Siguiente</button>'); // Agrega el botón Siguiente para el username
        }
    });

    // Cuando el campo de email está vacío, borra de abajo 
    form.on('input', '#email', function() {
        if (!$(this).val()) {
            $('#email').val('');
           // $('#siguienteBotonRegisterEmail').remove(); // Elimina el botón Siguiente para el email
            $('#password').val('');
            $('#password').parent().remove(); // Elimina el campo de password
          //  $('#siguienteBotonRegisterPassword').remove(); // Elimina el botón Siguiente para el password
            $('#confirmPassword').val('');
            $('#confirmPassword').parent().remove(); // Elimina el campo de repetir password
          //  $('#siguienteBotonRegisterConfirmPassword').remove(); // Elimina el botón Siguiente del campo repetir password
            $('#telephone').val('');
            $('#telephone').parent().remove(); // Elimina el campo de tlf
          //  $('#siguienteBotonRegisterTelephone').remove(); // Elimina el botón Siguiente del campo tlf
            $('#country').val('');
            $('#country').parent().remove(); // Elimina el campo de PAIS
          //  $('#siguienteBotonRegisterCountry').remove(); // Elimina el botón Siguiente del campo PAIS
            $('#city').val('');
            $('#city').parent().remove(); // Elimina el campo de city
           // $('#siguienteBotonRegisterCity').remove(); // Elimina el botón Siguiente del campo city
            $('#zipcode').val('');
            $('#zipcode').parent().remove(); // Elimina el campo de codigo postal
           // $('#siguienteBotonRegister').remove(); // Elimina el botón Siguiente del campo REGISTRARSE
           // form.append('<button id="siguienteBotonRegisterEmail" type="button">Siguiente</button>'); // Agrega el botón Siguiente para el username
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