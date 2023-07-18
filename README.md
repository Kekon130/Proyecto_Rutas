# Proyecto_OpenStreetMap
El propósito de este proyecto es determinar la distancia y el tiempo que esta tarda una persona en llegar a un punto determinado, ya sea en transporte público o privado, conociendo su domicilio.

## Antes de empezar a usar
Para usar correctamente este sistema se debe crear dentro del directorio un fichero con el nombre "config.ini".

Este fichero tendra la forma: ***nombre_variable = "valor_variable"***. Dentro del fichero se tendrán que declarar las variables:
* **destino:** en esta variable se debe guardar la ubicación del lugar que se quiere usar como referencia para el destino que servirá tanto para el cálculo de distancias como para el cálculo de rutas.
* **email:** en esta variable se debe almacenar un email que servirá para poder realizar peticiones a la API de OpenStreetMap.
* **openroute_key:** en esta varibale se debe guardar la API_key que se debe generar [dando de alta](https://openrouteservice.org/dev/#/signup) una cuenta en la pagina de Openrouteservice. Esta clave servirá para hacer las peticiones a la API de Openrouteservice.
## Como funciona
Lo primero que se necesita es una cuenta de cada uno de los servicios de los que se sirve el programa para determinar la ubicación y las rutas. Estos son: