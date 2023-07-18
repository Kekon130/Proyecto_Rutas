# Proyecto_OpenStreetMap
El propósito de este proyecto es determinar la distancia y el tiempo que esta tarda una persona en llegar a un punto determinado, ya sea en transporte público o privado, conociendo su domicilio.

## Antes de empezar a usar
Para usar correctamente este sistema se debe crear dentro del directorio un fichero con el nombre "config.ini".

Dentro del fichero se tendrán que declarar, respetando los nombre que aparecen a continuación y el formato descrito en el apartado de formatos, las variables:
* **destino:** en esta variable se debe guardar la ubicación del lugar que se quiere usar como referencia (respetando el formato descrito en el apartado de Formatos) para el destino que servirá tanto para el cálculo de distancias como para el cálculo de rutas.
* **email:** en esta variable se debe almacenar un email que servirá para poder realizar peticiones a la [API de OpenStreetMap](https://nominatim.org/release-docs/develop/api/Overview/).
* **openroute_key:** en esta varibale se debe guardar la API_key que se puede generar [dando de alta](https://openrouteservice.org/dev/#/signup) una cuenta en la página de Openrouteservice. Esta clave servirá para hacer las peticiones a la [API de Openrouteservice](https://openrouteservice.org/dev/#/api-docs).
* **here_public_transport_key:** en esta variable se debe almacenar la API_key que se puede generar [dando de alta](https://platform.here.com/sign-up?step=verify-identity) una cuenta en la página de HERE. Esta clave servirá para hacer las peticiones a la [API de HERE public transit](https://developer.here.com/documentation/public-transit/dev_guide/index.html).

Todas las cuentas mencionadas anteriormente tienen un coste 0, aunque si se desea expandir su utilidad es posible obtener unos mejores planes, para mas informacioón consultar las respectivas webs de las APIs.
## Como funciona
Todas las rutas de la API requieren de un solo parametro llamado ***origen***, este parametro almacena la ubicació (que debe respetar el formato definido en el apartado de Formatos) desde la cual se desea medir la distancia o la ruta hasta el el punto de destino previamente definido.

A continuación se va a explicar cada una de las rutas de la API:
* **/distanciaLineaRecta?origen=ubicación:**: esta ruta devuelve un JSON en el que se indíca la distancia en línea recta entre las 2 ubicaciones. *Ej: {"distancia":"37.49"}*
* **/distanciaCoche?origen=ubicación:** esta ruta devuelve un JSON en el que se indíca la distancia y el tiempo de la ruta más rápida en coche que podría tomar una persona para ir desde el punto de origen hasta el punto de destino.
* **/distanciaTransportePublico?origen=ubicación:** esta ruta devuele un JSON en el que se indíca la distancia y el tiempo de la ruta más rápida en transporte público que puede tomar una persona para ir desde el punto de origen hasta el punto de destino.
* **/informacionDistancia?origen=ubicación:** esta ruta devuelve un JSON en el que se incluye todo lo que devuelven las otras rutas.

## Formatos
* **Formato de las entradas del fichero config.ini:** *nombre_variable = "valor_variable"*
* **Formato de las ubicaciones:** *"nombre de la calle", "código postal", "ciudad en la que se encuentra", "comunidad autónoma en la que se encuentra"*

## Ejemplos de entradas/salidas
* **/distanciaLineaRecta**
  * *Entrada:* http://localhost:8080/distanciaLineaRecta?origen=Terminal 4, 28042, Barajas, Madrid, Comunidad de Madrid
  * *Salida:* {"distancia":"11.74"}
* **/distanciaCoche**
  * *Entrada:* http://localhost:8080/distanciaCoche?origen=Terminal 4, 28042, Barajas, Madrid, Comunidad de Madrid
  * *Salida:* {"distancia":"16.25","duracion":{"horas":0,"minutos":16,"segundos":44}}

* **/distanciaTransportePublico**
  * *Entrada:* http://localhost:8080/distanciaTransportePublico?origen=Terminal 4, 28042, Barajas, Madrid, Comunidad de Madrid
  * *Salida:* {"distancia":"19.03","tiempo":{"horas":0,"minutos":57,"segundos":0}}

* **/informacionDistancia**
  * *Entrada:* http://localhost:8080/informacionDistancia?origen=Terminal 4, 28042, Barajas, Madrid, Comunidad de Madrid
  * *Salida:* {"linea recta":{"distancia":"11.74"},"coche":{"distancia":"16.25","duracion":{"horas":0,"minutos":16,"segundos":44}},"transporte publico":{"distancia":"19.03","tiempo":{"horas":0,"minutos":57,"segundos":0}}}

