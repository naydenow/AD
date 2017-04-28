
<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<style type="text/css">
	.spotlight .image img {
	    border-radius: 0;
	    width: 100%;
	    height: 34vh;
    	opacity: 0.7;
	}

	.spotlight .image img:hover {
		opacity: 0.9;
	}

	.spotlight .image {
		background-color: #000;
	}

	 #map {
            width: 100%; height: 100vh; padding: 0; margin: 0;
        }

    #three {
    	padding: 0;
    }

</style>

				<!-- Two -->
					<section id="two" class="wrapper alt style2">
						<section class="spotlight">
							<div class="image">
								<a href="/company/technocom">
									<img src="/images/ned2.jpg" alt="" />
								</a>
							</div><div class="content">
								<h2>Группа компаний Техноком</h2>
								<p>Консолидация активов и проектного управления реализуемых договоров.</p>
							</div>
						</section>
						<section class="spotlight">
							<div class="image">
								<a href="/company/uslepip">
									<img src="/images/pic02.jpg" alt="" />
								</a>
								</div><div class="content">
								<h2>Управление строительства линий электропередачи и подстанций
								</h2>
								<p>основным направлением деятельности компании является энергетическое строительство в регионах Сибири и Дальнего Востока.</p>
							</div>
						</section>
						<section class="spotlight">
							<div class="image">
								<a href="/company/vpnedvizhimost">
									<img src="images/lep2.jpg" alt="" />
								</a>

								</div><div class="content">
								<h2>ВП-Недвижимость</h2>
								<p>общестроительные работы в направлении промышленной и коммерческой недвижимости с 2011г.</p>
							</div>
						</section>
					</section>

				<!-- Three -->
					<section id="three" class="wrapper style3 special">

							<div id="map"></div>

					</section>

				<!-- CTA -->
					<section id="cta" class="wrapper style4">
						<div class="inner">
							<header>
								<h2>Группа компаний «Техноком»</h2>
								<p>Создана в 2017 году для консолидации активов и проектного управления реализуемых договоров.</p>

							</header>
							<ul class="actions vertical">
<!-- 								<li><a href="/map" class="button fit special">ЗОНА ПОКРЫТИЯ</a></li>
 -->								<li><a href="/company/technocom" class="button fit">Узнать больше</a></li>
							</ul>
						</div>
					</section>


				<!-- Footer -->
	<script type="text/javascript">
		var objects = <?=json_encode($this->arData)?>;
		ymaps.ready(function () {
			var myMap = new ymaps.Map('map', {
		        center: [55.751574, 37.573856],
		        zoom: 4,
		        scrollZoom:false
		    }, {
		        searchControlProvider: 'yandex#search'
		    });

			myMap.behaviors
				.disable(['scrollZoom'])



    myMap.options.set('scrollZoomSpeed', 0.5);

			var createPlacemark = function(obj){
				return new ymaps.Placemark([obj.lat, obj.long], {
			            balloonContent: obj.title,
			            iconCaption: obj.title
			        }, {
			            preset: 'islands#greenDotIconWithCaption'
			        });
			}


			objects.forEach(function(obj){
				myMap.geoObjects.add(createPlacemark(obj))
			});
		});
	</script>
