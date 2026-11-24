# APG Aviso de Garantía Legal

Contributors: artprojectgroup

Donate link: https://artprojectgroup.es/tienda/donacion

Tags: legal guarantee, consumer rights, woocommerce, eu, conformity

Requires at least: 6.0

Tested up to: 7.2
Requires PHP: 7.4

Stable tag: 0.5.0

WC requires at least: 7.0

WC tested up to: 11.3.1
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
- Seis colocaciones, cada una con sus propios ajustes: un botón flotante en seis posiciones posibles, el último elemento de cualquier menú, el pie, Finalizar compra encima del botón de realizar el pedido, la ficha de producto bajo el botón de añadir al carrito y el carrito.
- En Finalizar compra, la ficha y el carrito, la frase que abre el aviso o el aviso completo sin nada que pulsar, que es lo que pide parte de la doctrina alemana.
- La etiqueta GARAN de la garantía comercial de durabilidad, por producto y por variación, dibujada sobre la plantilla oficial de la Comisión con la duración, el productor y el modelo, o la etiqueta que te haya dado el productor. Sale en la ficha, antes de realizar el pedido, en el carrito y en el correo del pedido.
- Fuera de las compras hechas sólo de productos virtuales, porque la garantía legal cubre bienes.
- Una nota privada en cada pedido con lo que se le mostró al cliente: cómo y en qué idioma salió el aviso en Finalizar compra, las etiquetas GARAN y el correo que llevó el PDF oficial.
- Cada colocación elige su estilo —texto, icono y texto, o solo icono— y sus colores, colores al pasar el ratón y tamaño de letra, todos partiendo de "heredar del tema".
- En los correos de pedido al cliente, que es lo que piden también las guías, con el PDF oficial adjunto en el idioma del cliente.
- El enlace clicable a Your Europe que debe acompañar al aviso, en el idioma que toca.
- Una nota nacional junto al aviso para los tres años de garantía legal de España y Portugal y los tres años para reclamar de Suecia, que el aviso europeo no puede indicar por no ser editable.
- Tus propias condiciones de garantía, con un texto de partida que editas, y un botón que crea una página con ambas cosas y la selecciona como tu página de condiciones.
- Shortcodes `[apg_guarantee_notice]`, `[apg_guarantee_terms]` y `[apg_guarantee_button]`, los mismos tres como bloques y un bloque de la etiqueta GARAN para la plantilla de producto.
- Un recordatorio de la garantía legal y la etiqueta GARAN de cada línea en las facturas PDF de PDF Invoices & Packing Slips for WooCommerce.
- Hooks para ocultar el aviso por ubicación y por cliente, en tiendas que también venden a profesionales, cambiar sus textos y el adjunto del correo, y pintar tu propio contenido antes, después o en lugar del aviso.
- Te avisa en el escritorio y en Salud del sitio cuando no hay ninguna colocación activa y el aviso no estaría llegando a nadie.
- Compatible con WPML y Polylang para los textos que escribes, mediante `wpml-config.xml` y registro de cadenas en tiempo de ejecución.
- El navegador cachea el aviso un año, así que cuesta una petición por visitante.
- Funciona con WooCommerce y sin él: con él el aviso llega a Finalizar compra y a los correos de pedido, y sin él todo lo demás sigue funcionando.
- El plugin no fija ningún color, borde ni tipografía salvo que se lo pidas, así que hereda la estética de tu tema, también en temas oscuros.
- Ayuda contextual dentro del plugin, en la pestaña _Ayuda_ de la pantalla de ajustes, con un panel por cada bloque y enlace a la documentación completa.

### Traducciones

- Español ([**Art Project Group**](https://artprojectgroup.es/)).
- English ([**Art Project Group**](https://artprojectgroup.es/)).

### Soporte técnico

**APG Aviso de Garantía Legal** es un plugin gratuito. **Art Project Group** no presta soporte técnico gratuito, pero ofrece un servicio de [soporte técnico](https://artprojectgroup.es/tienda/soporte-tecnico) de pago para la instalación y configuración.

### Más información

Puedes obtener más información sobre **APG Aviso de Garantía Legal** en nuestro [sitio web oficial](https://artprojectgroup.es/plugins-para-wordpress/apg-aviso-de-garantia-legal) y seguir el desarrollo en [GitHub](https://github.com/artprojectgroup/apg-legal-guarantee-notice).

Tienes la [documentación completa](https://artprojectgroup.es/documentacion/apg-aviso-de-garantia-legal), con cada ajuste explicado uno a uno.

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

No, y el plugin no te va a dejar. El Reglamento de Ejecución (UE) 2025/1960 fija su diseño y su contenido, y las guías dicen que no se puede distorsionar ni recortar. El plugin incluye los ficheros de la propia Comisión y los sirve byte a byte, tal y como salen de su paquete oficial.

### Mi país concede una garantía más larga que los dos años del aviso.

Para eso está la nota nacional. El aviso indica el mínimo europeo y no puede decir otra cosa, así que el plugin imprime tu texto nacional al lado, nunca dentro. En una tienda de España, Portugal o Suecia viene activada con el texto de ese país, y puedes cambiarlo y enlazarlo a tus condiciones. El país sale de la ubicación de la tienda en WooCommerce, nunca del idioma del sitio.

### ¿Necesita JavaScript?

No. El aviso se abre con la API popover nativa, así que funciona con el teclado, con un lector de pantalla y con los scripts desactivados. El foco entra en el modal al abrirse y Escape lo cierra.

### ¿Qué idiomas cubre?

Los 24 oficiales de la UE que publica la Comisión: alemán, búlgaro, checo, croata, danés, eslovaco, esloveno, español, estonio, finés, francés, griego, húngaro, inglés, irlandés, italiano, letón, lituano, maltés, neerlandés, polaco, portugués, rumano y sueco. Los idiomas sin aviso propio recaen en uno razonable, y el filtro `apg_guarantee_language` permite forzar la elección.

### ¿Y la etiqueta GARAN?

La etiqueta GARAN de la UE señala una garantía comercial de durabilidad que ofrece gratis el **productor**, para el producto entero y por más de dos años. Ofrecerla es decisión del productor; mostrarla, cuando el productor la ofrece, es obligación del vendedor. Rellena la pestaña *EU GARAN* del producto con la duración, el productor y el identificador del modelo que te dé el productor, y el plugin dibuja con ellos la etiqueta oficial, en la tipografía Inter que piden las guías. Cada variación puede llevar los suyos. Si el productor te ha dado la etiqueta ya rellena, súbela en la misma pestaña.

### ¿Tengo que mostrar el aviso completo en Finalizar compra?

Las guías de la Comisión ilustran una frase que abre el aviso al primer clic, y es lo que viene por defecto. Parte de la doctrina alemana interpreta el artículo 246a EGBGB como que en Finalizar compra hay que mostrar el aviso entero, sin nada que pulsar. Si vendes en Alemania o Austria, o prefieres ir sobre seguro, elige *El aviso completo* en *Cómo se muestra*.

### También vendo a profesionales. ¿Puedo ocultarles el aviso?

Sí. El aviso solo se debe a los consumidores, pero WordPress y WooCommerce no distinguen a un consumidor de un profesional, y cada plugin mayorista o B2B los marca a su manera. Por eso el plugin te lo pregunta con el filtro `apg_guarantee_show_notice`, una vez por ubicación. Por ejemplo, para ocultarlo al rol `wholesale_customer`:

```php
add_filter( 'apg_guarantee_show_notice', function ( $show, $context, $order ) {
	$user_id = $order ? $order->get_customer_id() : get_current_user_id();
	return $show && ! user_can( $user_id, 'wholesale_customer' );
}, 10, 3 );
```

Los contextos son `cart`, `checkout`, `email`, `email_attachment`, `float`, `footer`, `invoice`, `menu`, `product` y `shortcode`, y `$order` solo llega en los del correo y la factura. En los correos la decisión es exacta, porque el pedido dice quién compró; en el resto depende de que el cliente esté identificado, ya que a un visitante anónimo no se le puede distinguir y una caché de página completa les sirve a todos la misma página.

Para pintar tus condiciones para profesionales donde se ha ocultado el aviso, usa la acción `apg_guarantee_notice_hidden`. El resto de hooks:

- `apg_guarantee_before_notice` y `apg_guarantee_after_notice` (acciones): contenido alrededor del aviso en Finalizar compra, en el correo y en `[apg_guarantee_notice]`. Reciben `$context`, `$order` y `$plain_text`, igual que `apg_guarantee_notice_hidden`.
- `apg_guarantee_trigger_text`: la frase que abre el aviso, con `$context` (`panel` para el título del modal).
- `apg_guarantee_your_europe_link_text`: el texto del enlace a Your Europe.
- `apg_guarantee_national_note_text`: la nota nacional; una cadena vacía la quita.
- `apg_guarantee_email_attachment_path`: el fichero adjunto al correo; una cadena vacía no adjunta nada.
- `apg_guarantee_has_goods`: si una compra contiene bienes, para un catálogo que no encaje en la regla «lo que no es virtual es un bien».
- `apg_guarantee_national_note_countries`: los países que llevan nota nacional.
- `apg_guarantee_garan_data`: los datos GARAN de un producto; null deja la etiqueta fuera.
- `apg_guarantee_invoice_document_types`: los tipos de documento de factura que llevan el recordatorio.

### ¿Esto es lo mismo que el botón de desistimiento?

No. Aquel es el artículo 11 bis, que añadió la Directiva (UE) 2023/2673, y lo cubre nuestro [APG Desistimiento para WooCommerce](https://wordpress.org/plugins/apg-withdrawal-for-woocommerce/). Este plugin cubre el artículo 22 bis, que es una obligación distinta. Puedes usar los dos a la vez.

## Changelog

### 0.5.0
- Nueva etiqueta GARAN por producto y por variación, dibujada sobre la plantilla de la Comisión y mostrada en la ficha, antes de realizar el pedido, en el carrito y en el correo del pedido.
- Finalizar compra puede mostrar ya el aviso completo sin nada que pulsar, en vez de la frase que lo abre.
- Nuevas colocaciones en la ficha de producto y en el carrito, desactivadas por defecto.
- El aviso se deja fuera de las compras hechas sólo de productos virtuales.
- Cada pedido guarda una nota privada con lo que se le mostró al cliente.
- Notas nacionales para Portugal y Suecia, además de España.
- Nuevos bloques del aviso, el botón, las condiciones y la etiqueta GARAN.
- Nueva comprobación en Salud del sitio, y la garantía en las facturas PDF de PDF Invoices & Packing Slips for WooCommerce.
- El enlace a Tu Europa lleva ya a la misma página que el QR del aviso: en finés, húngaro, letón, lituano y rumano llevaba a una página inexistente.
- Probado con WooCommerce 11.3.1, y «Tested up to» va sólo en el readme, como exige ya Plugin Check.

### 0.4.1
- Los enlaces a la web, a la documentación, a la donación y al soporte se abren en el idioma de quien mira la pantalla.

### 0.4.0

* Nueva ayuda contextual en la pantalla de ajustes, con un panel por cada bloque y enlace a la documentación completa.
* El aviso de que el aviso legal no se está mostrando en ningún sitio ya no sale dos veces en la pantalla del plugin.

### 0.3.0

* Nuevos hooks para ocultar el aviso por ubicación y por cliente (por ejemplo, a profesionales), cambiar sus textos y el adjunto del correo, y añadir tu propio contenido antes, después o en lugar del aviso.
* La nota nacional del correo ahora se traduce con WPML y Polylang, igual que en la web.

### 0.2.0

* WooCommerce deja de ser obligatorio: sin él los ajustes pasan a Ajustes y se ocultan las ubicaciones de checkout y correo.
* El correo de pedido del cliente lleva ahora adjunto el PDF oficial del aviso, en el idioma del cliente.

### 0.1.1

* La nota de los tres años de España ahora depende del país de la tienda y no del idioma del sitio.

### 0.1.0

- Versión inicial.

## Gracias

Gracias a todas las personas que usan el plugin, ayudan a mejorarlo, hacen una donación o nos animan con sus comentarios.

Si te resulta útil este plugin, puedes apoyar su desarrollo con una [pequeña donación](https://artprojectgroup.es/tienda/donacion).

## Los ficheros del aviso

Los ficheros de imagen y los PDF de `assets/notices/` son el aviso armonizado oficial sobre la garantía legal de conformidad, tal y como lo publica la Comisión Europea y como lo fija el Reglamento de Ejecución (UE) 2025/1960. No son obra del plugin ni están cubiertos por su licencia GPL: son documentos de la Comisión Europea, reutilizados al amparo de la Decisión 2011/833/UE, que autoriza la reutilización de los documentos de la Comisión de forma gratuita siempre que se cite la fuente. La fuente se cita aquí y junto al propio aviso, que enlaza al portal Tu Europa de la Comisión.

El plugin no los edita nunca, no los redibuja y no los redimensiona: los empaqueta tal y como llegaron y sirve los bytes que le dieron. Veintitrés idiomas van en el PNG de la Comisión y el inglés en su SVG, porque su paquete raster no trae el inglés y el vectorial sí. El PDF de cada idioma es el que se adjunta al correo de pedido.

Los ficheros de `assets/garan/` son la plantilla de la Comisión de la etiqueta GARAN, de la misma fuente y al amparo de la misma Decisión. El reglamento deja editables tres de sus campos (la duración, el productor y el identificador del modelo), y son los únicos en los que escribe el plugin; el texto de ejemplo de esos tres campos se ha quitado de la plantilla para poder rellenarlos. Las versiones raster que se usan en los correos son esa plantilla renderizada tal cual.

La etiqueta va en Inter, la tipografía que piden las guías, de The Inter Project Authors, con licencia SIL Open Font License 1.1 (`assets/garan/inter-OFL.txt`). El plugin incluye un subconjunto con los caracteres que necesita la etiqueta.

## Servicios externos

Los ficheros del aviso y la plantilla GARAN viajan dentro del plugin y no se descarga nada para servirlos. Las etiquetas GARAN se dibujan en tu propio servidor y se guardan en tu carpeta de subidas.

El plugin hace una única petición a un servicio externo, y sólo ahí:

- **API de plugins de wordpress.org** (`https://api.wordpress.org/plugins/info/1.2/`). Se le pide la puntuación del propio plugin para mostrarla en la pantalla de ajustes. Ocurre sólo mientras un administrador está viendo esa pantalla, como mucho una vez al día, y la respuesta se cachea 24 horas. La petición lleva el slug del plugin y nada más: ningún dato personal, ningún dato del sitio, ningún dato del visitante. Nunca se hace en el frontend. Se rige por la [política de privacidad de WordPress.org](https://wordpress.org/about/privacy/) y por sus [términos](https://wordpress.org/about/privacy/). Si la petición falla, la pantalla dice simplemente que la puntuación es desconocida.

El aviso contiene un código QR, y el plugin imprime el enlace clicable equivalente; ambos apuntan al portal Your Europe de la Comisión Europea (`https://europa.eu/youreurope/...`). Son enlaces que el visitante puede decidir seguir; el plugin no solicita nada a ese sitio.
