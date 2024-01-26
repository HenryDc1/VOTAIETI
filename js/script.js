class ScrollController {
  constructor() { this.prevScrollY = window.scrollY; }

  isScrollingUp() {
      const scrollY = window.scrollY;
      const isUp = scrollY < this.prevScrollY;
      this.prevScrollY = scrollY;
      return isUp;
  }
}

let scrollController = new ScrollController();

function handleScroll() {
  if (scrollController.isScrollingUp()) {
      gsap.to(".contenedorHeader", { y: 0, duration: 0.3, ease: "power2.out" });
  } else {
      gsap.to(".contenedorHeader", { y: -100, duration: 0.3, ease: "power2.inOut" });
  }
}

window.addEventListener("scroll", handleScroll);




document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('tienescuentaBotonLogin').addEventListener('click', function() {
      window.location.href = 'register.php';
  });
});



// popup de error



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

function showConditionsPopup() {
  // Crear la ventana flotante
  var conditionsPopup = $('<div/>', {
      id: 'conditionsPopup',
      style: 'position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); background-color: #f44336; color: white; padding: 20px; border-radius: 5px;'
  });

  // Crear el mensaje
  var message = $('<p/>', {
      text: 'You must accept the conditions to proceed.'
  });

  // Crear el checkbox
  var checkbox = $('<input/>', {
      type: 'checkbox',
      id: 'conditionsCheckbox'
  });

  // Crear la etiqueta para el checkbox
  var label = $('<label/>', {
      text: ' I accept the conditions'
  });

  // Crear el botón "Next"
  var nextButton = $('<button/>', {
      text: 'Next',
      style: 'position: absolute; bottom: 0; right: 0; background-color: transparent; color: white; border: none; font-size: 20px; cursor: pointer;'
  });

  // Añadir el mensaje, el checkbox, la etiqueta y el botón "Next" a la ventana flotante
  conditionsPopup.append(message, checkbox, label, nextButton);

  // Añadir la ventana flotante al cuerpo del documento
  $('body').append(conditionsPopup);

  // Manejador de eventos para el botón "Next"
  nextButton.click(function() {
      // If the checkbox is checked
      if ($('#conditionsCheckbox').is(':checked')) {
          // Redirect to the dashboard
          window.location.href = 'dashboard.php';
      } else {
          // Otherwise, show an alert
          alert('You must accept the conditions to proceed.');
      }
  });
}