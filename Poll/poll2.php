
        <!DOCTYPE html>
        <html lang="en">
       
        
        <head>
            <link rel="stylesheet" href="../styles.css">
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Encuesta 2</title>
            <style>
            .bodyVota {
                margin: 0;
                padding: 0;
            }

            .bodyVota .imagenCabecera {
                padding: 200px;
                background-image: url("../imgs/votacion.jpg");
            }

            .imagenCabecera h1 {
                margin-bottom: -30px;
                font-family: "Playfair Display", serif;
                font-size: 100px;
                color: #EDF2F4;
                text-align: center;
            }
            .vota {
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding-top: 100px;
                padding-bottom: 100px;
                margin: 0;
               
               
            }

           .vota .options {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
                justify-items: center;
            }

            
            .options label {
                font-size: 20px !important; /* Aumenta el tamaño del texto de las opciones */
            }

            
            img {
                width: 300px;
                height: 200px;
                padding-top: 10px;
            }
            .imgHeader {
                width: 80px;
                margin: 10px;
                height: 75px;
                transition-duration: 3s;
            }

            .logoimgFooter {
                width: 60px;
                height: 50px;
            }
            h1 {
                margin-bottom: 50px; /* Añade espacio debajo de la pregunta */
                font-family: "Playfair Display", serif; /* Añade el tipo de letra */
            }
            button {
                margin-top: 50px; /* Añade espacio encima del botón */
            }
            #botonEnviar {
                padding: 10px 20px;
                border-radius: 5px;
                background: linear-gradient(45deg, #EF233C 50%, #D80032 50%);
                background-size: 200% 200%;
                background-position: 100%;
                border: none;
                border-radius: 10px;
                font-family: "Lato", sans-serif;
                font-size: 15px;
                color: #EDF2F4;
                cursor: pointer;
                transition: background-position 1s, color 1s;
            }
            .vota button {
                align-self: center;
            }
            </style>
            </head>
            
             <body class="bodyVota">
            <div class="contenedorHeader">
                <?php include "../header.php"; ?>
            </div>

            <div class="contenedor">
            <div class="imagenCabecera">
                <h1>VOTAIETI</h1>
                <h2>Tu elección, nuestro compromiso global</h2>
            </div>

            <div class="vota">
            <?php  session_start();
            $guest_email = $_SESSION["guest_email"]; ?>

            
            
            <h1 >rfeferf</h1><form method="post" action="proces_votes.php" class="options"><input type="hidden" name="poll_id" value="2"><div><input type="radio" id="option3" name="pollOption" value="3"><label for="option3">dweddwed</label></div><div><input type="radio" id="option4" name="pollOption" value="4"><label for="option4">dsfsf</label></div><div style="grid-column: span 2;"><button type="submit" id="botonEnviar">Enviar</button></div></form></div><div class="contenedorFooter"><?php include "../footer.php"; ?></div>