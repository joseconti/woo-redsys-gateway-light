=== Transferencia Online ===
Contributors: INESPAY
Donate link: https://es.inespay.com/transferenciaonline/
Tags: pagos, cobros, transferencia, psd2, checkout, woocomerce, pasarela, ecommerce, iniciación de pagos, inespay
Requires at least: 4.6
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Redireccione a su cliente a su banco para que autorice un pago mediante transferencia bancaria en tiempo real.

== Description ==

### **La nueva forma de aceptar pagos mediante transferencia bancaria. Autorizado y supervisado por Banco de España**

#### Pago en tiempo real
> Redireccione a su cliente a su banco para que autorice una transferencia en tiempo real.

#### Confirmación instantánea
> Notificación instantánea para confirmar el pago a su cliente y liberar los pedidos sin demora.

#### Fácil de usar
> El pagador sólo necesita disponer de Banca Online con su banco habitual.

#### Sin retrocesiones de pago
> Sin contracargos, retrocesiones o cancelaciones de pago.


### **Adecuado al comercio electrónico**

#### La mayor base de usuarios
> Los usuarios no necesitan registrarse para pagar. Tan sólo necesitan disponer de Banca Online con cualquier banco compatible.

#### Mayor conversión
> Habilita pagos de importe elevado al disponer de saldo en cuenta bancaria.

#### Pago directo de cuenta a cuenta
> Transferencia bancaria con destino directo a su cuenta bancaria habitual. Sin cuentas intermediarias.

#### Ahorro en costes administrativos
> Notificación instantánea a su Backoffice. Sin necesidad de conciliación manual de pago.

== Installation ==

### **Existen 2 métodos de instalación del plugin:**

#### Automática
- Ve a tu escritorio de WordPress, **accede al menú Plugins y pulsa el botón Añadir Nuevo.**
- **En el campo de búsqueda inserta las palabras "Transferencia Online"** para localizar el plugin.
- Pulsa el botón **Instalar y luego el botón Activar**. ¡Listo!

#### Manual
- **Descárgate el plugin** desde WordPress.
- **Sube el archivo zip vía FTP a la ruta "/wp-content/plugins"** o directamente desde el escritorio de WordPress accediendo al menú:
    Plugins > Añadir nuevo > Subir plugin
- Pulsa el botón **Instalar y luego el botón Activar**. ¡Listo!

### **Configuración**

1. Asegúrate de tener instalado el plugin de Transferencia Online para WooCommerce.
2. Una vez instalado nuestro módulo, accede al menú WooCommerce > Ajustes.
3. Selecciona la pestaña **"Pagos"** y te mostrará un submenú con los métodos de pago instalados en tu tienda WooCommerce. Selecciona la opción **TRANSFERENCIA ONLINE**.
4. Abre una página nueva a parte en tu navegador, accede o regístrate en el **Área de clientes de Transferencia Online** [Acceso Dashboard](https:/clients.inespay.com/build/signup)
5. Una vez dentro del Área de clientes de Transferencia Online selecciona la opción **Claves API y copia las 2 Claves de Test.**
6. Vuelve al escritorio de WordPress y selecciona Test en el selector de Entorno. **Pega las 2 Claves de Test (API Key y API Token)** copiadas en el paso anterior y haz clic en el botón Guardar.
7. Ve a tu tienda WooCommerce y **realiza todos los pagos de prueba que consideres oportuno** para comprobar que todo funciona correctamente.
8. **Una vez superadas todas las pruebas de pago, puedes solicitar las Claves en Producción.** Para ello, deberás iniciar sesión en el Área de clientes de Transferencia Online, selecciona la opción Mi Cuenta y rellenar el formulario Mis Datos, facilitando la información que se solicita en el mismo. En las siguientes 24 horas recibirás una confirmación por email acerca de la autorización para utilizar el servicio de pago Transferencia Online a través del plugin en tu tienda WooCommerce.
9. Por último, **inicia sesión en el Área de clientes de Transferencia Online y selecciona la opción Claves API.** En ese momento aparecerán las 2 Claves en Producción (API Key y API Token). Copia y pega dichas claves en los campos de Producción de tu escritorio de WordPress. Asegúrate de cambiar la opción Entorno a Real.

    ### ¡Hecho! 
    **Ya tienes configurada tu tienda WooCommerce para aceptar pagos mediante Transferencia Online.**

== Frequently asked questions ==

Para más información envía un email a support@team.inespay.com	

== Screenshots ==
1. Selecciona el método de pago Transferencia Online para pagar con tu banco.
2. Selecciona tu banco para ser redirigido.
3. Autentícate ante tu banco con tus claves habituales de Banca Online.
4. Comprueba el resumen de pago y autoriza el pago con el procedimiento habitual establecido por tu banco.
5. Pago completado.
6. Pedido realizado.
7. Configuración.

== Changelog ==

= 1.6.10 =
* Nueva opción de configuración para marcar automáticamente los pedidos como Procesando al confirmarse el pago (desactivada por defecto). Corrección del cambio de estado al confirmar pagos desde estados personalizados de Inespay. Mejora de los textos descriptivos de los campos de credenciales para aclarar que Test y Producción pueden estar rellenos simultáneamente.

= 1.6.9 =
* Nueva opción de configuración "Marcar pedido como Procesando": permite elegir si los pedidos se marcan automáticamente como Procesando al confirmarse el pago, o si se mantiene el estado personalizado "Transferencia Online: pago confirmado". La opción está activada por defecto.

= 1.6.8 =
* Actualización del correo de soporte en el readme.

= 1.6.7 =
* Corrección de traducciones.

= 1.6.6 =
* Corrección de traducciones.

= 1.6.5 =
* Corrección de traducciones.

= 1.6.4 =
* Actualización de versión.

= 1.6.3 =
* Corrección de la carga de traducciones: eliminadas búsquedas redundantes en directorios incorrectos heredadas de versiones antiguas de WooCommerce que impedían cargar correctamente el archivo de idioma del plugin.

= 1.6.2 =
* Corrección del sistema de traducciones: todas las cadenas de texto están ahora en inglés como clave base, permitiendo su correcta traducción a cualquier idioma.
* Añadidas traducciones al español de todas las cadenas nuevas introducidas en la versión 1.6.1.

= 1.6.1 =
* Credenciales separadas por entorno: nuevos campos independientes para API Key y API Token de Test y de Producción.
* Migración automática: al actualizar desde versiones anteriores, las credenciales existentes se copian automáticamente al entorno correspondiente según la configuración previa.
* Confirmación de pago en dos fases: el pedido se marca como pendiente al volver del banco y solo se confirma definitivamente al recibir la notificación del servidor de INESPAY, evitando confirmaciones prematuras.
* Validación de credenciales de producción: si el entorno seleccionado es Real, el plugin verifica que las credenciales de producción estén rellenas antes de guardar la configuración.
* Compatibilidad con PHP 8.2+: declaración explícita de propiedades de clase y eliminación de propiedades dinámicas deprecadas.
* Compatibilidad con WooCommerce hasta 10.4.3 y WordPress hasta 6.9.
* Corrección de codificación de URLs en callbacks para evitar problemas con caracteres especiales.
* Mejoras en el sistema de logging para facilitar el diagnóstico de incidencias.

= 1.5 =
* Adaptaciones para el correcto funcionamiento en la versión 6.6.x de WordPress.

= 1.4 =
* Adaptaciones para el correcto funcionamiento en la versión 5.9.x de WordPress.

== Upgrade notice ==

= 1.6.10 =
Mejoras en la gestión del estado del pedido tras la confirmación del pago y nueva opción de configuración para marcarlo como Procesando automáticamente.

= 1.6.9 =
Nueva opción para controlar el estado del pedido tras la confirmación del pago. Activada por defecto: los pedidos pasarán a estado Procesando automáticamente.

= 1.6.2 =
Corrección interna del sistema de traducciones. No requiere ninguna acción por parte del usuario.

= 1.6.1 =
Esta versión separa las credenciales de Test y Producción en campos independientes. Al actualizar, tus credenciales anteriores se migrarán automáticamente al entorno que tenías configurado. Revisa la configuración del gateway tras la actualización para confirmar que todo es correcto.