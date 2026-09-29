// assets/js/acceso.js
// Comportamiento exclusivo de la pantalla de acceso.

(function () {
    function iniciarAcceso() {
        // Elementos de la pantalla de acceso
        const btnIniciarSesion = document.getElementById("btn__iniciar-sesion");
        const btnRegistrarse = document.getElementById("btn__registrarse");

        const formularioLogin = document.querySelector(".formulario__login");
        const formularioRegister = document.querySelector(".formulario__register");
        const contenedorFormularios = document.querySelector(".contenedor__login-register");
        const cajaLogin = document.querySelector(".caja__trasera-login");
        const cajaRegister = document.querySelector(".caja__trasera-register");

        // Ahora sí es válido usar return: estamos dentro de una función.
        // Si este script se carga en otra página, no registra eventos.
        if (
            !btnIniciarSesion ||
            !btnRegistrarse ||
            !formularioLogin ||
            !formularioRegister
        ) {
            return;
        }

        btnIniciarSesion.addEventListener("click", mostrarLogin);
        btnRegistrarse.addEventListener("click", mostrarRegistro);
        window.addEventListener("resize", anchoPage);

        function anchoPage() {
            if (!cajaLogin || !cajaRegister) return;

            if (window.innerWidth > 850) {
                cajaRegister.style.display = "block";
                cajaLogin.style.display = "block";
            } else {
                cajaRegister.style.display = "block";
                cajaRegister.style.opacity = "1";
                cajaLogin.style.display = "none";

                formularioLogin.style.display = "block";
                formularioRegister.style.display = "none";

                if (contenedorFormularios) {
                    contenedorFormularios.style.left = "0px";
                }
            }
        }

        function mostrarLogin() {
            if (!cajaLogin || !cajaRegister || !contenedorFormularios) return;

            if (window.innerWidth > 850) {
                formularioLogin.style.display = "block";
                formularioRegister.style.display = "none";
                contenedorFormularios.style.left = "10px";

                cajaRegister.style.opacity = "1";
                cajaLogin.style.opacity = "0";
            } else {
                formularioLogin.style.display = "block";
                formularioRegister.style.display = "none";
                contenedorFormularios.style.left = "0px";

                cajaRegister.style.display = "block";
                cajaLogin.style.display = "none";
            }
        }

        function mostrarRegistro() {
            if (!cajaLogin || !cajaRegister || !contenedorFormularios) return;

            if (window.innerWidth > 850) {
                formularioRegister.style.display = "block";
                formularioLogin.style.display = "none";
                contenedorFormularios.style.left = "410px";

                cajaRegister.style.opacity = "0";
                cajaLogin.style.opacity = "1";
            } else {
                formularioRegister.style.display = "block";
                formularioLogin.style.display = "none";
                contenedorFormularios.style.left = "0px";

                cajaRegister.style.display = "none";
                cajaLogin.style.display = "block";
                cajaLogin.style.opacity = "1";
            }
        }

        // Evita envíos repetidos por doble clic.
        const formulariosProtegidos = [];

        window.addEventListener("pageshow", function () {
            formulariosProtegidos.forEach(function (entrada) {
                entrada.enviando = false;

                const boton = entrada.form.querySelector('button[type="submit"]');
                if (boton) boton.disabled = false;
            });
        });

        function protegerEnvio(formulario) {
            const entrada = { form: formulario, enviando: false };
            formulariosProtegidos.push(entrada);

            formulario.addEventListener("submit", function (evento) {
                if (entrada.enviando) {
                    evento.preventDefault();
                    return;
                }

                document.querySelector(".acceso-aviso-wrap")?.remove();
                entrada.enviando = true;

                const boton = formulario.querySelector('button[type="submit"]');
                if (boton) boton.disabled = true;
            });
        }

        anchoPage();
        protegerEnvio(formularioLogin);
        protegerEnvio(formularioRegister);
    }

    // Funciona tanto si el script está en <head> como si está al final del HTML.
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", iniciarAcceso);
    } else {
        iniciarAcceso();
    }
})();
