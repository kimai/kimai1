/**
 * translation for: xajax v.x.x
 * @version: 1.0.0
 * @author: mic <info@joomx.com>
 * @copyright xajax project
 * @license GNU/GPL
 * @package xajax x.x.x
 * @since v.x.x.x
 * save as UTF-8
 */

if ('undefined' != typeof xajax.debug) {
	/*
		Array: text
	*/
  xajax.debug.text = [];
  xajax.debug.text[100] = 'ALERTA: ';
  xajax.debug.text[101] = 'ERROR: ';
  xajax.debug.text[102] = 'MENSAJE XAJAX DEBUG:\n';
  xajax.debug.text[103] = '...\n[RESPUESTA LARGA]\n...';
  xajax.debug.text[104] = 'ENVIANDO PETICION';
  xajax.debug.text[105] = 'ENVIADO [';
  xajax.debug.text[106] = ' bytes]';
  xajax.debug.text[107] = 'LLAMADA: ';
  xajax.debug.text[108] = 'URI: ';
  xajax.debug.text[109] = 'INICIALIZANDO PETICION';
  xajax.debug.text[110] = 'PROCESANDO PARAMETROS [';
  xajax.debug.text[111] = ']';
  xajax.debug.text[112] = 'NO HAY PARAMETROS A PROCESAR';
  xajax.debug.text[113] = 'PREPARANDO PETICION';
  xajax.debug.text[114] = 'INICIANDO XAJAX CALL (En desuso: use xajax.request)';
  xajax.debug.text[115] = 'INICIANDO XAJAX REQUEST';
  xajax.debug.text[116] = 'Ningún procesador de respuesta esta disponible para tratar la respuesta del servidor.\n';
  xajax.debug.text[117] = '.\nRevisa mensajes de error del servidor.';
  xajax.debug.text[118] = 'RECIBIDO [status: ';
  xajax.debug.text[119] = ', size: ';
  xajax.debug.text[120] = ' bytes, time: ';
  xajax.debug.text[121] = 'ms]:\n';
  xajax.debug.text[122] = 'El servidor retorno el siguiente estado HTTP: ';
  xajax.debug.text[123] = '\nRECIBIDO:\n';
  xajax.debug.text[124] = 'El servidor retorno una redireccion a:<br />';
  xajax.debug.text[125] = 'HECHO [';
  xajax.debug.text[126] = 'ms]';
  xajax.debug.text[127] = 'INICIALIZANDO PETICION OBJETO';
   
  xajax.debug.exceptions = [];
  xajax.debug.exceptions[10001] = 'Invalid response XML: La respuesta contiene una etiqueta desconocida: {data}.';
  xajax.debug.exceptions[10002] = 'GetRequestObject: XMLHttpRequest no disponible, xajax esta deshabilitado.';
  xajax.debug.exceptions[10003] = 'Queue overflow: No se puede colocar objeto en cola porque esta llena.';
  xajax.debug.exceptions[10004] = 'Invalid response XML: La respuesta contiene una etiqueta o texto inesperado: {data}.';
  xajax.debug.exceptions[10005] = 'Invalid request URI: URI invalida o perdida; autodeteccion fallida; por favor especifica una explicitamente.';
  xajax.debug.exceptions[10006] = 'Invalid response command: Orden de respuesta mal formado recibido.';
  xajax.debug.exceptions[10007] = 'Invalid response command: Comando [{data}] no es un comando conocido.';
  xajax.debug.exceptions[10008] = 'Elemento con ID [{data}] no encontrado en el documento.';
  xajax.debug.exceptions[10009] = 'Invalid request: Nombre parametro de funcion perdido.';
  xajax.debug.exceptions[10010] = 'Invalid request: Objeto parametro de funcion perdido.';
}

if ('undefined' != typeof xajax.config) {
  if ('undefined' != typeof xajax.config.status) {
    /*
      Object: update
    */
    xajax.config.status.update = function() {
      return {
        onRequest: function() {
          window.status = 'Enviando Peticion...';
        },
        onWaiting: function() {
          window.status = 'Esperando Respuesta...';
        },
        onProcessing: function() {
          window.status = 'Procesando...';
        },
        onComplete: function() {
          window.status = 'Hecho.';
        }
      }
    }
  }
}