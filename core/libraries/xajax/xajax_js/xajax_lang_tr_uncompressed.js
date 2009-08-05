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
	
	xajax.debug.text = [];
	xajax.debug.text[100] = 'IKAZ: ';
	xajax.debug.text[101] = 'HATA: ';
	xajax.debug.text[102] = 'XAJAX DEBUG (HATA AYIKLAMASI) MESAJI:\n';
	xajax.debug.text[103] = '...\n[UZUN YANIT]\n...';
	xajax.debug.text[104] = 'ISTEK GÖNDERILIYOR';
	xajax.debug.text[105] = 'GÖNDERILDI [';
	xajax.debug.text[106] = ' byte]';
	xajax.debug.text[107] = 'ÇAGIRILIYOR: ';
	xajax.debug.text[108] = 'URI: ';
	xajax.debug.text[109] = 'ISTEK BASLATILIYOR';
	xajax.debug.text[110] = 'PARAMETRELER ISLENIYOR [';
	xajax.debug.text[111] = ']';
	xajax.debug.text[112] = 'ISLENECEK PARAMETRE YOK';
	xajax.debug.text[113] = 'ISTEK HAZIRLANIYOR';
	xajax.debug.text[114] = 'XAJAX ÇAGRISI BASLATILIYOR (kullanimi tavsiye edilmiyor: yerine xajax.request kullanin)';
	xajax.debug.text[115] = 'XAJAX ISTEGI BASLATILIYOR';
	xajax.debug.text[116] = 'Sunucudan gelen cevabi isleyecek cevap islemcisi yok.\n';
	xajax.debug.text[117] = '.\nSunucudan gelen hata mesajlarini kontrol edin.';
	xajax.debug.text[118] = 'ALINDI [durum: ';
	xajax.debug.text[119] = ', boyut: ';
	xajax.debug.text[120] = ' byte, süre: ';
	xajax.debug.text[121] = 'ms]:\n';
	xajax.debug.text[122] = 'Sunucu asagidaki HTTP durumunu gönderdi: ';
	xajax.debug.text[123] = '\nALINDI:\n';
	xajax.debug.text[124] = 'Sunucu su adrese yönlendirme istegi gönderdi :<br />';
	xajax.debug.text[125] = 'TAMAMLANDI [';
	xajax.debug.text[126] = 'ms]';
	xajax.debug.text[127] = 'ISTEK NESNESI BASLATILIYOR';
	
	/*
		Array: exceptions
	*/
	xajax.debug.exceptions = [];
	xajax.debug.exceptions[10001] = 'Geçersiz XML cevabi: Cevap bilinmeyen bir etiket tasiyor: {data}.';
	xajax.debug.exceptions[10002] = 'GetRequestObject: XMLHttpRequest hazir degil, xajax nesnesi etkisizlestirildi.';
	xajax.debug.exceptions[10003] = 'Islem kuyrugu fazla yüklendi: Kuyruk dolu oldugu için nesne kuyruga eklenemiyor.';
	xajax.debug.exceptions[10004] = 'Geçersiz XML cevabi: Cevap bilinmeyen bir etiket veya metin tasiyor: {data}.';
	xajax.debug.exceptions[10005] = 'Geçersiz istek URI: Geçersiz veya kayip URI; otomatik tespit yapilamadi; lütfen açikça bir tane belirleyiniz.';
	xajax.debug.exceptions[10006] = 'Geçersiz cevap komutu: Bozulmus cevap komutu alindi.';
	xajax.debug.exceptions[10007] = 'Geçersiz cevap komutu: [{data}] komutu bilinmiyor.';
	xajax.debug.exceptions[10008] = '[{data}] ID li element dosya içinde bulunamadi.';
	xajax.debug.exceptions[10009] = 'Geçersiz istek: Fonksiyon isim parametresi eksik.';
	xajax.debug.exceptions[10010] = 'Geçersiz istek: Fonksiyon nesne parametresi eksik.';
}

if ('undefined' != typeof xajax.config) {
	if ('undefined' != typeof xajax.config.status) {
		/*
			Object: update
		*/
		xajax.config.status.update = function() {
			return {
				onRequest: function() {
					window.status = 'İstek Gönderiliyor...';
				},
				onWaiting: function() {
					window.status = 'Cevap Bekleniyor...';
				},
				onProcessing: function() {
					window.status = 'İşlem Devam Ediyor...';
				},
				onComplete: function() {
					window.status = 'Tamamlandı.';
				}
			}
		}
	}
}
