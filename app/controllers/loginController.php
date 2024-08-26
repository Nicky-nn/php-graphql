<?php

namespace app\controllers;

use app\models\mainModel;

class loginController extends mainModel
{
    /*----------  Controlador iniciar sesion  ----------*/
    public function iniciarSesionControlador()
    {
        $usuario = $this->limpiarCadena($_POST['email']);
        $clave = $this->limpiarCadena($_POST['password']);

        # Verificando campos obligatorios #
        if ($clave == "") {
            echo '<article class="message is-danger">
				  <div class="message-body">
				    <strong>Ocurrió un error inesperado</strong><br>
				    No has llenado todos los campos que son obligatorios.
				  </div>
				</article>';
        } else {
            # Verificando usuario #
            $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$usuario'");

            if ($check_usuario->rowCount() == 1) {
                $check_usuario = $check_usuario->fetch();

                if ($check_usuario['usuario_email'] == $usuario && password_verify($clave, $check_usuario['usuario_clave'])) {
                    // Autenticación exitosa con la base de datos
                    $_SESSION['id'] = $check_usuario['usuario_id'];
                    $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                    $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                    $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                    $_SESSION['foto'] = $check_usuario['usuario_foto'];
                    $_SESSION['caja'] = $check_usuario['caja_id'];

                    // Hacer la solicitud a la API de isipass
                    // URL de la API de isipass
                    $url = 'https://sandbox.isipass.net/api';
                    $data = [
                        'query' => 'mutation LOGIN {
                            login(shop: "sandbox", email: "' . $usuario . '", password: "' . $clave . '") {
                                token
                                refreshToken
                                perfil {
                                    nombres
                                    apellidos
                                    avatar
                                    miEmpresa {
                                        razonSocial
                                        codigoModalidad
                                        codigoAmbiente
                                        fechaValidezToken
                                        tienda
                                        email
                                        emailFake
                                    }
                                }
                            }
                        }'
                    ];

                    // Iniciar la solicitud cURL
                    $ch = curl_init($url);
                    // Configurar la solicitud cURL
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // Configurar la solicitud cURL para enviar datos JSON
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    // Configurar la solicitud cURL para enviar encabezados
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    $responseData = json_decode($response, true);

                    // Verificar si el inicio de sesión fue exitoso
                    // Ejemplo de Respuesta de la API de isipass
                    // {
                    //     "data": {
                    //         "login": {
                    //             "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJVMkZzZEdWa1gxOHdYMlFlNFZUMzFUMjVxajdmbkttalIySkJkWVNKS2swJTNEIiwiYWNjZXNzVHlwZSI6IlUyRnNkR1ZrWDElMkJYTUd4M0JJNUdSeG0zYnFyZ01aT2dPWDQ3RGNuRFc5VSUzRCIsInVzZXJJZCI6IjY0MjE4ZTVhMzUzNjg4ZTQ1ZTk3NDc1YiIsInJvbElkIjoiNjM5YzBlZGQ1ZGE4N2MxYzM5MWUxZGI3IiwiaWF0IjoxNzI0MzQwMjk2LCJleHAiOjE3MjY5MzIyOTZ9.fVH7XUQeUx8ZcHEyW3AUCTW-AtMVhjEwpqZBiXyd9C4",
                    //             "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvSWQiOiI2NDIxOGU1YTM1MzY4OGU0NWU5NzQ3NWIiLCJzdWIiOiJVMkZzZEdWa1gxJTJCVER1Rjl4bVNIY0JTZ1JRJTJGZEIzdzA3d3BHUSUyRkV4OTZjJTNEIiwiaWF0IjoxNzI0MzQwMjk2LCJleHAiOjE3MjQ5NDUwOTZ9.bNT2gToudNVb4Ypt-wM6dHikc33Vciy_a8WzRExjMD8",
                    //             "perfil": {
                    //                 "nombres": "Nick",
                    //                 "apellidos": "Yana",
                    //                 "avatar": "https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y",
                    //                 "miEmpresa": {
                    //                     "razonSocial": "INTEGRATE SANDBOX",
                    //                     "codigoModalidad": 1,
                    //                     "codigoAmbiente": 2,
                    //                     "fechaValidezToken": "31/12/2024",
                    //                     "tienda": "sandbox",
                    //                     "email": "jmquirogaf@gmail.com",
                    //                     "emailFake": "josuquemounfoco@gmail.com"
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }

                    if (isset($responseData['data']['login']['token'])) {
                        // Guardar la información en localStorage
                        echo '<script>
                            localStorage.setItem("isipass_token", "' . $responseData['data']['login']['token'] . '");
                            localStorage.setItem("isipass_refreshToken", "' . $responseData['data']['login']['refreshToken'] . '");
                            localStorage.setItem("isipass_nombre", "' . $responseData['data']['login']['perfil']['nombres'] . '");
                            localStorage.setItem("isipass_tienda", "' . $responseData['data']['login']['perfil']['miEmpresa']['tienda'] . '");
                            window.location.href = "' . APP_URL . 'dashboard/";
                        </script>';
                        $_SESSION['isipass_token'] = $responseData['data']['login']['token'];
                    } else {
                        // Mostrar modal de advertencia usando SweetAlert2
                        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                        echo '<script>
                            Swal.fire({
                                title: "Sin acceso a la API de isipass",
                                text: "No podrás facturar, pero puedes manejar el sistema.",
                                icon: "warning",
                                confirmButtonText: "Continuar"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "' . APP_URL . 'dashboard/";
                                }
                            });
                        </script>';
                    }
                } else {
                    echo '<article class="message is-danger">
								  <div class="message-body">
								    <strong>Ocurrió un error inesperado</strong><br>
								    Usuario o clave incorrectos.
								  </div>
								</article>';
                }
            } else {
                echo '<article class="message is-danger">
							  <div class="message-body">
							    <strong>Ocurrió un error inesperado</strong><br>
							    Usuario o clave incorrectos.
							  </div>
							</article>';
            }
        }
    }

    /*----------  Controlador cerrar sesion  ----------*/
    public function cerrarSesionControlador()
    {
        session_destroy();

        if (headers_sent()) {
            echo "<script> window.location.href='" . APP_URL . "login/'; </script>";
        } else {
            header("Location: " . APP_URL . "login/");
        }
    }
}
