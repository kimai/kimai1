/**
 * translation for: xajax v.x.x
 * @version: 1.0.0
 * @author: jeffrey <walkingsoul@gmail.com>
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
	xajax.debug.text[100] = 'FOUTMELDING: ';
	xajax.debug.text[101] = 'FOUT: ';
	xajax.debug.text[102] = 'XAJAX FOUTMELDINGS BERICHT:\n';
	xajax.debug.text[103] = '...\n[LANG ANTWOORD]\n...';
	xajax.debug.text[104] = 'VERZENDING AANVRAAG';
	xajax.debug.text[105] = 'VERZONDEN [';
	xajax.debug.text[106] = ' bytes]';
	xajax.debug.text[107] = 'AANROEPING: ';
	xajax.debug.text[108] = 'URI: ';
	xajax.debug.text[109] = 'INITIALISATIE AANVRAAG';
	xajax.debug.text[110] = 'VERWERKING PARAMETERS [';
	xajax.debug.text[111] = ']';
	xajax.debug.text[112] = 'GEEN PARAMETERS OM TE VERWERKEN';
	xajax.debug.text[113] = 'VOORBEREIDING AANVRAAG';
	xajax.debug.text[114] = 'BEGIN XAJAX AANVRAAG (verouderd: gebruik xajax.request)';
	xajax.debug.text[115] = 'BEGIN XAJAX AANVRAAG';
	xajax.debug.text[116] = 'Er is geen verwerkingsbestand gespecificeerd om de aanvraag te verwerken.\n';
	xajax.debug.text[117] = '.\nBekijk foutmeldingen van de server.';
	xajax.debug.text[118] = 'ONTVANGEN [status: ';
	xajax.debug.text[119] = ', omvang: ';
	xajax.debug.text[120] = ' bytes, Zeit: ';
	xajax.debug.text[121] = 'ms]:\n';
	xajax.debug.text[122] = 'De server retourneert de volgende HTTP-status: ';
	xajax.debug.text[123] = '\nONTVANGEN:\n';
	xajax.debug.text[124] = 'De server retourneert een doorverwijzing naar:<br />';
	xajax.debug.text[125] = 'KLAAR [';
	xajax.debug.text[126] = 'ms]';
	xajax.debug.text[127] = 'INITIALISATIE OBJECT AANVRAAG';

	/*
		Array: exceptions
	*/
	xajax.debug.exceptions = [];
	xajax.debug.exceptions[10001] = 'Ongeldig XML-antwoord: het antwoord bevat een onbekende tag: {data}.';
	xajax.debug.exceptions[10002] = 'GetRequestObject: XMLHttpRequest is niet beschikbaar, XajaX is uitgeschakeld.';
	xajax.debug.exceptions[10003] = 'Wachtrij limiet overschreden: kan het object niet in de wachtrij plaatsen, omdat die vol is.';
	xajax.debug.exceptions[10004] = 'Ongeldig XML-antwoord: het antwoord bevat een onverwachte tag of tekst: {data}.';
	xajax.debug.exceptions[10005] = 'Ongeldige Request-URI: Ongeldige of ontbrekende URI; automatische detectie faalt; specificeer een URI expliciet.';
	xajax.debug.exceptions[10006] = 'Ongeldig antwoord bevel: misvormd antwoord bevel ontvangen.';
	xajax.debug.exceptions[10007] = 'Ongeldig antwoord bevel: Bevel [{data}] is niet bekend.';
	xajax.debug.exceptions[10008] = 'Element met het ID [{data}] kon niet in het document worden gevonden.';
	xajax.debug.exceptions[10009] = 'Ongeldige aanvraag: Missende functie parameter - naam.';
	xajax.debug.exceptions[10010] = 'Ongeldige aanvraag: Missende functie parameter - object.';
}

if ('undefined' != typeof xajax.config) {
	if ('undefined' != typeof xajax.config.status) {
		/*
			Object: update
		*/
		xajax.config.status.update = function() {
			return {
				onRequest: function() {
					window.status = "Verzenden aanvraag...";
				},
				onWaiting: function() {
					window.status = "Wachten op antwoord...";
				},
				onProcessing: function() {
					window.status = "Verwerking...";
				},
				onComplete: function() {
					window.status = "Afgesloten.";
				}
			}
		}
	}
}
