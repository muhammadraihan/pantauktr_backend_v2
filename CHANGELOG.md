# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [2.4.1](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.4.0...v2.4.1) (2021-10-30)


### Bug Fixes

* Add origin request in log json ([803e1c4](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/803e1c418533a506d469646e2af913243bc0bd08))
* Order response to latest ([1607320](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/1607320d5dbaeb99425ea93dd5bfd8a9653bcb09))

## [2.4.0](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.3.2...v2.4.0) (2021-10-28)


### Features

* Add get single jenis pelanggaran data api endpoint ([4303826](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/4303826bd39cf635529477412430d3a50f061c30))
* Add new get single reference data endpoint ([6fe1a92](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/6fe1a92b43b483ccf8dffb9449b661e7d45cc2f7))
* Adding json response in auth api middleware ([6d985c0](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/6d985c005a8f0b7c3de3672954c95929e373abbe))
* Adding pelanggaran icon in report list api endpoint ([2aefa35](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/2aefa35109e766a4b6821e6e6ec0f925656d4ee9))
* Adding schedule command to prune revoked and expired token ([3655820](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/3655820a22a03e9b996ca3ba398e431d1133c20e))
* API v2 is deprecated migrate to API v3 ([10345cf](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/10345cf18dc49626ddf99ff18c0f3a69a7426249))
* Auth token now generated using laravel passport ([b414054](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/b4140549e21d57c8e069e7c9113a10c0b24bef32))
* Bentuk pelanggaran now relate to jenis pelanggaran ([af7831e](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/af7831ea25999b6e7cdba6470e288c0500962b62))
* Migrate endpoint url to version 3 ([f714e8a](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/f714e8a148648dbe21e5cb71a55bbdfc85bcd281))
* Send app error log to slack channel ([7a61f4d](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/7a61f4df049b625651dd9eafb865b98f3db1764f))
* Tracking device user when requesting api ([7669b0d](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/7669b0ddd82eaddcb4a17ce6ba3d2f8a3879bb58))


### Bug Fixes

* Add relation to pelanggaran in crud method ([bfd1c1c](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/bfd1c1c259c44d639f98475c942d7107f8e34300))
* Add static page permission ([af236bd](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/af236bd92f8eb083c998a31e33f71f631da59be5))
* Adding image icon in reference data ([22881d7](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/22881d73233556870ebfa10052841d945d9c9a2b))
* Change alamat column type to text for data too long error ([9d0ed80](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/9d0ed8009df92a23681068eb9259770c491ae389))
* Change how to generate token from social account ([f2b3a13](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/f2b3a132b2c11e0d1ce103bd5f1350bf540d47e7))
* Change json response and adding expires time decoder for passport refresh token ([277e648](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/277e648dc150f5211f7e5fcf3e0e2a3b06a39696))
* Check image in request if exist for update ([751eead](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/751eead6c23a5bbc0422370eabbf5d30353c0d59))
* Fix issue in slug column and change photo url in laporan to text ([3159f9a](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/3159f9ad5e51bbbca228a02bbb96d22f8c93b314))
* Fix redundan google bucket name ([dde95fd](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/dde95fd8155b210887af2ddb61c2e828c7e09f2a))
* Fix redundan google bucket name and change status logic ([154dd02](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/154dd02c4de3dba52f94db89d8a655cf5bb6dbf0))
* Fixing double bucket name when gcs url change to https ([fc5b8df](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/fc5b8df73802588c3904f7ed2ddce6836d0bc67f))
* Fixing double folder name when upload in GCS ([6056f07](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/6056f0786f50593a7a9982bc76e9d41c5c6ed8ca))
* Fixing error when OTP not exists ([937adae](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/937adae63cd55aa313a0d0ec27d60ddc4107a300))
* Fixing failed to generate session for social login ([3fe2256](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/3fe2256d1a7155cec5a6ba0dc274e53b86f2e922))
* Fixing failed to generate session for social login ([a2bc6fc](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/a2bc6fca32c05f7dfa274e69c2a7f997173aea7c))
* Fixing validation if image not exist in update ([55592b6](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/55592b6182895e88fffa3fe0ca698e0e2ae5bb1e))
* Fixing wrong array key in report detail endpoint ([0f82896](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/0f828965700dc075b82bb0ffc9ca57c950d91ac2))
* Fixing wrong parameter in report detail endpoint ([c4ee2c8](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/c4ee2c867e0b7f551d0a5aab6229d8df2d9dccb6))
* Remove id from reference api response ([d3fbb89](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/d3fbb898d3ee4cd6fbe00d91aa42f542ded2c939))
* Remove image in laporan api endpoint, upload image now handled from app ([41b8cea](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/41b8cea82f77729fcd44e70d688dc7d377af5483))
* Return laravel passport token when user registered and instances logged in, revoke all tokens when user loggout ([195744a](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/195744aae55a911eb993527aa211ae2c25db357c))
* Social account login now using resolver from social grant ([1c788c8](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/1c788c8e43a4caf8f3b92fc02a170a3587b8e2b6))

### [2.3.2](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.3.1...v2.3.2) (2021-07-24)


### Bug Fixes

* Fix remote deploy webhook ([247c1fd](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/247c1fd9716224a063c5dd4209116f1f23dedea7))

### [2.3.1](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.3.0...v2.3.1) (2021-07-01)


### Bug Fixes

* Fixing search in datatable ([ae4d611](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/ae4d6111984e712bf0023db38fa9ab3ca3197282))

## [2.3.0](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.8...v2.3.0) (2021-06-05)


### Features

* Add some additional permission for seedin ([6e3ce79](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/6e3ce79590484f943a9cd6fe833d71ea25817ed2))
* Adding additional permission data seeder ([102a66d](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/102a66d66f397fbaef0b9e1f824f32264ac2c301))
* Adding operator type data seeder ([7704275](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/7704275be8d363b0264be7fa686489ed11cf6289))


### Bug Fixes

* Fixing action permission ([b468336](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/b46833682edec6a606251f0be476f32f8a98d4ec))
* Fixing permission issue ([6a676b6](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/6a676b6272ac12ae2970cbd0f26bf5d884f6ecaa))
* Fixing permission issue ([b01e271](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/b01e271379b20910adc6ba0a4c9c27c0a002e23f))
* Fixing permission issue ([4daa57f](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/4daa57f764039636d10ad0554d1c80e94d4a696a))
* Fixing permission issue ([8cdf0e6](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/8cdf0e639b55db7e4360fd4effbe3c551ea20dc1))
* Fixing permission, change date format to id, and [hoto url to google storage ([22bc056](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/22bc056cb8ba678942f68d7850090b62536f5735))
* Hide admin role in create ([b49990f](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/b49990f10daea6c89ac7b2a0e631f793326066cd))
* Option city only show for pemda role only ([85971ae](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/85971ae7c120f25447869955457bbd467bf46efd))
* Reset filter bug and change pdf export to builtin themes datatable button ([3cd56c1](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/3cd56c1e3bc4b65b93c836fd9357ab5dbb3be4bc))

### [2.2.8](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.7...v2.2.8) (2021-04-22)


### Bug Fixes

* Fixing jenis laporan to id in relation ([ccf9050](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/ccf9050de747a88a3127889f15235b6d227c268a))

### [2.2.7](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.6...v2.2.7) (2021-04-22)


### Bug Fixes

* Fix timezone in app ([f92021d](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/f92021df0257fd45d6afac665f02525da6392735))

### [2.2.6](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.5...v2.2.6) (2021-04-18)


### Features

* Adding deploy script for production ([510905b](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/510905b1962332999502790e6935f1881fd0bbcb))

### [2.2.5](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.4...v2.2.5) (2021-04-17)


### Bug Fixes

* Order by latest link added ([64aa8e9](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/64aa8e913ffdde1bc8d0a4efaf2f5c76dd32b8ef))

### [2.2.4](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.3...v2.2.4) (2021-04-16)


### Bug Fixes

* Avatar on null in profile api ([4fd0579](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/4fd05796455ed101a8c6928d08b2a58c22c4721e))

### [2.2.3](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.2...v2.2.3) (2021-04-15)


### Bug Fixes

* Fixing null value in report detail api ([1d72593](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/1d725936ed831ea3ce8bf388cac9ce548bebd1ff))

### [2.2.2](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.1...v2.2.2) (2021-04-15)

### [2.2.1](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.2.0...v2.2.1) (2021-04-08)


### Bug Fixes

* Fix jWT token error when logout ([9d63283](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/9d632834c3a7bb50f344dce5ed4a523dcda7703f))

## [2.2.0](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.1.0...v2.2.0) (2021-04-06)


### Features

* Adding generate report number function ([1816e2c](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/1816e2c2045f8c3273c0f9b704945fcf5b78dc1b))
* Now support update name in api endpoint ([d6ac856](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/d6ac8564e762fb7c57db56422366414bf78d8ff9))


### Bug Fixes

* Adding nomor_laporan column ([a73e9ee](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/a73e9eebfde6e2cc7185ec68f6cee6e7bc618406))
* Adding nomor_laporan in fillable ([e47e784](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/e47e784a4351453cadedc18b37ab2e1c4da5733e))
* Adding place name and address request from apps ([fd72f77](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/fd72f771c89df8db35cb218fc62669ad2ea66f2a))

## [2.1.0](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.1.0-4...v2.1.0) (2021-04-05)

## [2.1.0-4](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.1.0-3...v2.1.0-4) (2021-03-20)


### Features

* Add count jumlah laporan and reward point when pelapor post laoran ([be8a232](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/be8a232a5a761def333fefc76c3e6eaf3e6698a2))

## [2.1.0-3](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.1.0-2...v2.1.0-3) (2021-03-18)


### Features

* Upload image laporan to google storage ([51695dd](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/51695ddf4f0d8eb559d92cdf1cdcdd704b9dc652))

## [2.1.0-2](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.1.0-1...v2.1.0-2) (2021-03-17)


### Bug Fixes

* Fixing token json response ([6669bfd](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/6669bfd90221b851409114249813300a1fe7fd98))

## [2.1.0-1](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.1.0-0...v2.1.0-1) (2021-02-11)

## [2.1.0-0](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/compare/v2.0.0...v2.1.0-0) (2021-02-09)


### Features

* Add forgot password api endpoint ([f59397d](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/f59397d8f222bbe58ae96dfd4e9652f8abeecc8c))
* Add forgot password method ([d8b3160](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/d8b3160edc11747c40df7d611fe35f4a88478a1f))

## 2.0.0 (2021-02-05)


### Features

* Adding basic data seeder ([1cab052](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/1cab05235748c492f13c8446ae6e7c4ed322c799))
* Adding manual register features ([c0bcccc](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/c0bcccc93b7abffd419006e74df134c6549bc03b))
* Kota automated seed based in province code ([16b1614](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/16b16144f044c179f066b82a28fe13c5dcf92c65))
* Refresh tokens endpoint added ([056a3a8](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/056a3a8efc38b2cf78eec26cb9fca8a59b341ed7))


### Bug Fixes

* Adding JWT aliases and provider in config ([bdb0121](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/bdb01213ceb20a0acaacf733e6cfe799497dec3e))
* Adding log activity and JWT auth support ([bd8572c](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/bd8572cd7d56219972ec19fd67869a623265c086))
* Adding nullble in reward point column ([67af8a8](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/67af8a898130561dfe25063669e929b6d0b91fe7))
* Adding request arguments in method ([7fc0596](https://git.wbaindonesia.com/wbaindonesia/pantauktr_backend_v2/commit/7fc0596fd6cc070a2c31397bf0275e190cc4e661))

## [1.2.0](https://git.wbaindonesia.com/wbaindonesia/starterkit/compare/v1.1.0...v1.2.0) (2020-12-08)


### Features

* Add Models directory ([02add1a](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/02add1a59942e793e59ace847b575f78c33a213c))
* Adding namespace ([94a7d1d](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/94a7d1d5aac956c7f330dc41919596f6d68d6f5b))
* Adding reset password ([ae65b96](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/ae65b96d6e16a5b81f6e45f36c66374b80c03b99))
* Now support log activity ([f08da73](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/f08da7334ac88894bba3eacb71e0856e04e8afcc))
* Update to Laravel 8 ([b3809d0](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/b3809d015fc9894e61e76e63e0e1fc7e53b05ac8))
* Update to Laravel 8 ([2a5a1c6](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/2a5a1c630c84292731f1900cda2b8aa9a0324514))


### Bug Fixes

* Show avatar to default if not set by user ([b8a5c8d](https://git.wbaindonesia.com/wbaindonesia/starterkit/commit/b8a5c8d88914608d8b225780251516b60c0939c9))

## [1.1.0](https://git.fiotech.co/wbaindonesia/starterkit/compare/v1.0.0...v1.1.0) (2020-07-18)


### Features

* Change app version read from package.json ([1151921](https://git.fiotech.co/wbaindonesia/starterkit/commit/115192149792d636ba51d417a55a4f29bd5c4f56))

## 1.0.0 (2020-03-07)
