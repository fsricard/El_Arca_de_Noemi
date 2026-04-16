<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo mensaje desde la web</title>
</head>

<body style="margin:0; padding:0; background-color:#FFF8F5; font-family: 'Poppins', Arial, sans-serif; color:#6b6a6a;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#FFF8F5; padding:20px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; box-shadow:0 6px 18px rgba(15,23,42,0.08); overflow:hidden;">

                    <tr>
                        <td>
                            <img src="cid:cid_header"
                                 alt="Cabecera El Arca de Noemí"
                                 style="width:100%; display:block;">
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <img src="cid:cid_logo"
                                 alt="Logo El Arca de Noemí"
                                 style="width:120px; height:auto;">
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:10px 30px;">
                            <h2 style="margin:0; font-family:'Quicksand', Arial, sans-serif; color:#2A6432;">
                                Nuevo mensaje desde la página de contacto
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 30px; font-size:15px; line-height:1.6;">

                            <p style="margin:0 0 15px 0;">
                                Hola Noemí, has recibido un nuevo mensaje a través del formulario de contacto.
                            </p>

                            <div style="background:#fafafa; border-radius:10px; padding:15px; border:1px solid #e0e0e0;">
                                <p style="margin:0;"><strong style="color:#2A6432;">Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
                                <p style="margin:0;"><strong style="color:#2A6432;">Email:</strong> <?= htmlspecialchars($email) ?></p>
                                <p style="margin:0;"><strong style="color:#2A6432;">Asunto:</strong> <?= htmlspecialchars($asunto) ?></p>
                                <p style="margin:0;"><strong style="color:#2A6432;">Fecha:</strong> <?= htmlspecialchars($fecha_envio) ?></p>
                            </div>

                            <h3 style="margin-top:25px; font-family:'Quicksand', Arial, sans-serif; color:#2A6432;">
                                Mensaje del usuario:
                            </h3>

                            <div style="background:#fff7e6; border-radius:10px; padding:15px; border:1px solid #ffe0b3;">
                                <?= nl2br(htmlspecialchars($mensaje)) ?>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:20px; background:#BDC453; color:#ffffff; font-size:13px; border-radius:0 0 10px 10px;">
                            Este correo se ha generado automáticamente desde la web de El Arca de Noemí.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>