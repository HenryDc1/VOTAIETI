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


function showSuccesPopup(message) {
  // Crear la ventana flotante
  var successPopup = $('<div/>', {
      id: 'successPopup',
      text: message,
      style: 'position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); background-color: green; color: white; padding: 20px; border-radius: 5px;'
  });

  // Crear el botón 'X'
  var closeButton = $('<button/>', {
      text: 'X',
      style: 'position: absolute; top: 0; right: 0; background-color: transparent; color: white; border: none; font-size: 20px; cursor: pointer;'
  });

  // Añadir el botón 'X' a la ventana flotante
  successPopup.append(closeButton);

  // Añadir la ventana flotante al cuerpo del documento
  $('body').append(successPopup);

  // Manejador de eventos para el botón 'X'
  closeButton.click(function () {
      successPopup.remove();
  });
}


$(document).ready(function() {
  $(".more").on("click", function() {
    // cambiar la visibilidad de complete
    $(".complete").toggle();

    // cambiar el texto del boton dependiendo del texto actual
    if ($(this).text() == "Ocultar") {
      $(this).text("Spoiler");
    } else {
      $(this).text("Ocultar");
    }
  });
});