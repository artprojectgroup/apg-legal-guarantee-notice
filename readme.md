# APG Aviso de Garantía Legal

Contributors: artprojectgroup

Donate link: https://artprojectgroup.es/tienda/donacion

Tags: legal guarantee, consumer rights, woocommerce, eu, conformity

Requires at least: 6.0

Tested up to: 7.2

Requires PHP: 7.4

Stable tag: 0.1.0

WC requires at least: 7.0

WC tested up to: 11.1.2

License: GNU General Public License v3 or later

License URI: https://www.gnu.org/licenses/gpl-3.0.html

Muestra el aviso oficial de la UE sobre la garantía legal de conformidad, obligatorio desde el 27 de septiembre de 2026, en los 24 idiomas oficiales de la UE.

## Descripción

Desde el **27 de septiembre de 2026**, el artículo 22 bis de la Directiva 2011/83/UE obliga a toda tienda que venda bienes a consumidores a mostrar de forma destacada el **aviso armonizado oficial de la UE sobre la garantía legal de conformidad**. El Reglamento de Ejecución (UE) 2025/1960 fija su diseño y su contenido, y el aviso no se puede editar, recortar ni redibujar.

**APG Aviso de Garantía Legal** incluye el aviso tal como lo publica la Comisión Europea, en los 24 idiomas oficiales de la UE, y le muestra a cada visitante el suyo. No hay nada que diseñar ni nada que redactar.

### Características

- El aviso oficial en los 24 idiomas oficiales de la UE, servido sin alterar.
- El idioma sigue al visitante, no al sitio: una tienda que se lee en alemán sirve el aviso en alemán.
- El catalán, el euskera y el gallego recaen en el aviso en español, no en el inglés.
- Se abre en un modal al primer clic, el patrón que ilustran las guías prácticas de la Comisión, con la API popover nativa y sin nada de JavaScript.
- Cuatro colocaciones, cada una con sus propios ajustes: un botón flotante en seis posiciones posibles, el último elemento de cualquier menú, el pie y Finalizar compra encima del botón de realizar el pedido.
- Cada colocación elige su estilo —texto, icono y texto, o solo icono— y sus colores, colores al pasar el ratón y tamaño de letra, todos partiendo de "heredar del tema".
- En los correos de pedido al cliente, que es lo que piden también las guías.
- El enlace clicable a Your Europe que debe acompañar al aviso, en el idioma que toca.
- Una nota nacional junto al aviso para los tres años de garantía legal del artículo 120.1 del TRLGDCU en España, que el aviso europeo no puede indicar por no ser editable.
- Tus propias condiciones de garantía, con un texto de partida que editas, y un botón que crea una página con ambas cosas y la selecciona como tu página de condiciones.
- Shortcodes `[apg_guarantee_notice]`, `[apg_guarantee_terms]` y `[apg_guarantee_button]`.
- Te avisa en el escritorio cuando no hay ninguna colocación activa y el aviso no estaría llegando a nadie.
- Compatible con WPML y Polylang para los textos que escribes, mediante `wpml-config.xml` y registro de cadenas en tiempo de ejecución.
- El navegador cachea el aviso un año, así que cuesta una petición por visitante.
- El plugin no fija ningún color, borde ni tipografía salvo que se lo pidas, así que hereda la estética de tu tema, también en temas oscuros.

### Traducciones

- Español ([**Art Project Group**](https://artprojectgroup.es/)).
- English ([**Art Project Group**](https://artprojectgroup.es/)).

### Soporte técnico

**APG Aviso de Garantía Legal** es un plugin gratuito. **Art Project Group** no presta soporte técnico gratuito, pero ofrece un servicio de [soporte técnico](https://artprojectgroup.es/tienda/ticket-de-soporte) de pago para la instalación y configuración.

## Instalación

1. Puedes:

- Subir la carpeta `apg-legal-guarantee-notice` al directorio `/wp-content/plugins/` vía FTP.
- Subir el archivo ZIP completo vía _Plugins -> Añadir nuevo -> Subir_ en el Panel de Administración de WordPress.
- Buscar **APG Legal Guarantee Notice** en el buscador disponible en _Plugins -> Añadir nuevo_ y pulsar el botón _Instalar ahora_.

2. Activar el plugin a través del menú _Plugins_.
3. El botón flotante aparece desde ese momento, y por sí solo ya cumple la obligación. Revisa _WooCommerce -> Garantía legal_ para moverlo, cambiarle el aspecto o colocarlo en otro sitio.

## Preguntas frecuentes

### ¿Tengo que hacer algo para que el aviso cumpla?

No. Al activar el plugin queda un botón flotante en todas las páginas de tu tienda, que es el "recordatorio general en el sitio web del vendedor" que describen las guías prácticas de la Comisión, a nivel de tienda. Finalizar compra y el correo de pedido vienen activados también porque las guías ilustran ambos. Si desactivas todas las colocaciones, el plugin te avisa en el escritorio.

### ¿Puedo editar el aviso, traducirlo por mi cuenta o recortarlo?

No, y el plugin no te va a dejar. El Reglamento de Ejecución (UE) 2025/1960 fija su diseño y su contenido, y las guías dicen que no se puede distorsionar ni recortar. El plugin incluye los ficheros de la propia Comisión y los sirve byte a byte. Viajan comprimidos con gzip solo para que el plugin ocupe poco; gzip no pierde nada, así que lo que sale de tu servidor es lo que publicó la Comisión.

### Mi país concede una garantía más larga que los dos años del aviso.

Para eso está la nota nacional. El aviso indica el mínimo europeo y no puede decir otra cosa, así que el plugin imprime tu texto nacional al lado, nunca dentro. En un sitio en español viene activada, indicando los tres años del artículo 120.1 del TRLGDCU, y puedes cambiar el texto y enlazarlo a tus condiciones.

### ¿Necesita JavaScript?

No. El aviso se abre con la API popover nativa, así que funciona con el teclado, con un lector de pantalla y con los scripts desactivados. El foco entra en el modal al abrirse y Escape lo cierra.

### ¿Qué idiomas cubre?

Los 24 oficiales de la UE que publica la Comisión: alemán, búlgaro, checo, croata, danés, eslovaco, esloveno, español, estonio, finés, francés, griego, húngaro, inglés, irlandés, italiano, letón, lituano, maltés, neerlandés, polaco, portugués, rumano y sueco. Los idiomas sin aviso propio recaen en uno razonable, y el filtro `apg_guarantee_language` permite forzar la elección.

### ¿Y la etiqueta GARAN?

Es otra cosa y es voluntaria. La etiqueta GARAN de la UE señala una garantía comercial de durabilidad que ofrece gratis el **fabricante**, para el producto entero y por más de dos años. Es decisión del fabricante, no del vendedor, así que la mayoría de tiendas no tienen nada que hacer con ella. Puede que llegue a este plugin más adelante.

### ¿Esto es lo mismo que el botón de desistimiento?

No. Aquel es el artículo 11 bis, que añadió la Directiva (UE) 2023/2673, y lo cubre nuestro [APG Desistimiento para WooCommerce](https://wordpress.org/plugins/apg-withdrawal-for-woocommerce/). Este plugin cubre el artículo 22 bis, que es una obligación distinta. Puedes usar los dos a la vez.

## Changelog

### 0.1.0

- Versión inicial.

## Gracias

Gracias a todas las personas que usan el plugin, ayudan a mejorarlo, hacen una donación o nos animan con sus comentarios.

Si te resulta útil este plugin, puedes apoyar su desarrollo con una [pequeña donación](https://artprojectgroup.es/tienda/donacion).

## Los ficheros del aviso

Los 24 SVG de `assets/notices/` son el aviso armonizado oficial sobre la garantía legal de conformidad, tal y como lo publica la Comisión Europea y como lo fija el Reglamento de Ejecución (UE) 2025/1960. No son obra del plugin ni están cubiertos por su licencia GPL: son documentos de la Comisión Europea, reutilizados al amparo de la Decisión 2011/833/UE, que autoriza la reutilización de los documentos de la Comisión de forma gratuita siempre que se cite la fuente. La fuente se cita aquí y junto al propio aviso, que enlaza al portal Tu Europa de la Comisión.

El plugin no los edita nunca. Viajan comprimidos con gzip sólo para que la descarga sea pequeña, y gzip no pierde nada, así que los bytes que salen de tu servidor son los que publicó la Comisión.

## Servicios externos

Los ficheros del aviso viajan dentro del plugin y no se descarga nada para servirlos.

El plugin hace una única petición a un servicio externo, y sólo ahí:

- **API de plugins de wordpress.org** (`https://api.wordpress.org/plugins/info/1.2/`). Se le pide la puntuación del propio plugin para mostrarla en la pantalla de ajustes. Ocurre sólo mientras un administrador está viendo esa pantalla, como mucho una vez al día, y la respuesta se cachea 24 horas. La petición lleva el slug del plugin y nada más: ningún dato personal, ningún dato del sitio, ningún dato del visitante. Nunca se hace en el frontend. Se rige por la [política de privacidad de WordPress.org](https://wordpress.org/about/privacy/). Si la petición falla, la pantalla dice simplemente que la puntuación es desconocida.

El aviso contiene un código QR, y el plugin imprime el enlace clicable equivalente; ambos apuntan al portal Your Europe de la Comisión Europea (`https://europa.eu/youreurope/...`). Son enlaces que el visitante puede decidir seguir; el plugin no solicita nada a ese sitio.
