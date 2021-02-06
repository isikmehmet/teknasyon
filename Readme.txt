Sistem gereksinimleri:
1. php 7.4
2. phalcon 4
3. redis 5.3.3-7.4-ts-vc15-x64 (dll)
4. redis 3.2.100 x64 (msi)

Kurulum:
1. uygulama yolu, "app/config/config.php:29" üzerinden kurulum yapılacak dizin ile değiştirilmeli
2. kullanılacak veri tabanı için gerekli bağlantı bilgileri "app/config/config.php:11" üzerinde bulunmaktadır.


Kullanım:
1. Api
	1.1. Register
		1. Url: http://localhost/teknasyon/Api/Register (Not: proje dizinlerini değiştirmeyi unutmayın)
		2. Bağlantı türü: Post
		3. Request(İstek) Parametreleri:
			1. uid: int(20)
			2. appId: int(20)
			3. language: string(50)
			4. os: string(150)
		4. Response(Cevap) Parametreleri: (Json)
			1. Register: ("OK" - false) => false boolean, "OK" string olarak döner. false dönerse client_token dönmez
			2. client_token: string
	1.2. Purchase
		1. Url: http://localhost/teknasyon/Api/Purchase (Not: proje dizinlerini değiştirmeyi unutmayın)
		2. Bağlantı türü: Post
		3. Request(İstek) Parametreleri:
			1. client_token: string(50)
			2. receipt: string(255)
		4. Response(Cevap) Parametreleri: (Json)
			1. Response: (OK - false) => false boolean, "OK" string olarak döner. false dönerse başka parametre dönmez
			2. service_response: Json => mock platformunun dönüşünü Json olarak döndürür
				2.1. Result: (OK - false) => false boolean, "OK" string olarak döner. false dönerse expire-date dönmez
				2.2. Status: boolean
				2.3. expire-date: string(datetime)
	1.3. Check Subscription
		1. Url: http://localhost/teknasyon/Api/CheckSubscription (Not: proje dizinlerini değiştirmeyi unutmayın)
		2. Bağlantı türü: Post
		3. Request(İstek) Parametreleri:
			1. client_token: string(50)
		4. Response(Cevap) Parametreleri: (Json)
			1. Response: boolean
2. Worker
	1. Windows için "app.vbs" dosyasının görev zamanlayıcı üzerinden 5 dakikalık kurulması gerekir.
		Not: proje dizinlerini değiştirmek için "app.bat" içerisindeki dosya yollarını güncellemelisiniz.
	2. Centos, ubuntu, vs.. (bash ile çalışan herhangi bir os) için crontab'a aşağıdaki satırlar eklenmeli
		1. */5 * * * * php C:\xampp\htdocs\teknasyon\public\index.php Worker Check iOS
		2. */5 * * * * php C:\xampp\htdocs\teknasyon\public\index.php Worker Check Google
		Not: proje dizinlerini değiştirmeyi unutmayın.
	3. test edebilmek için tek seferlik çalıştırmak isterseniz konsol ekranınıza aşağıdaki kodu yazmanız yeterli olacaktır.
		1. php C:\xampp\htdocs\teknasyon\public\index.php Worker Check Google
		Not: proje dizinlerini değiştirmeyi unutmayın.