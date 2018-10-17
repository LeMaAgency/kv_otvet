$(document).ready(function () {
    $('.top-slider').owlCarousel({
        items: 1,
        margin: 0,
        loop: true,
        nav: true,
        dots: true,
        autoplay: true
    });


    $('.filter-price-slider').each(function (i, el) {
        var doubleHandleSlider = el;
        var minValInput = $(doubleHandleSlider).siblings('.filter-price').find('.filter-min-value-input').get(0);
        var maxValInput = $(doubleHandleSlider).siblings('.filter-price').find('.filter-max-value-input').get(0);
        var snapValues = [
            $(doubleHandleSlider).find('.filter-min-value'),
            $(doubleHandleSlider).find('.filter-max-value')
        ];

        if (doubleHandleSlider !== null) {
            var defaultMin = 0,
                defaultMax = 999999999999,
                start = +($(el).data('current-min') || defaultMin),
                end = +($(el).data('current-max') || defaultMax),
                min = +($(el).data('min') || defaultMin),
                max = +($(el).data('max') || defaultMax);

            // Price Slider
            noUiSlider.create(doubleHandleSlider, {
                start: [start, end],
                connect: true,
                step: 1,
                range: {
                    'min': [min],
                    'max': [max]
                }
            });
            doubleHandleSlider.noUiSlider.on('change', function (values, handle) {
                var rangeValues = values;
                $(minValInput).val(rangeValues[0]);
                $(maxValInput).val(rangeValues[1]);
            });

            doubleHandleSlider.noUiSlider.on('update', function (values, handle) {
                snapValues[handle].innerHTML = values[handle];
                $(minValInput).val(values[0]);
                $(maxValInput).val(values[1]);
            })

            $(minValInput).on('change', function () {
                doubleHandleSlider.noUiSlider.set([this.value, null]);
            });

            $(maxValInput).on('change', function () {
                doubleHandleSlider.noUiSlider.set([null, this.value]);
            })
        }
    });

    // Select
    $(document).on('click', '.filter-select-link', function () {
        var dropBlock = $(this).parent().find('.filter-select-drop');
        if (dropBlock.is(':hidden')) {
            dropBlock.slideDown();
            $(this).addClass('active filter-border-color');
            $('.filter-select-drop').find('li').click(function () {
                var selectResult = $(this).html(),
                    selectValue = $(this).data('value');
                $(this).closest('.filter-select').find('input').val(selectValue);
                $(this).closest('.filter-select').find('.filter-select-link').removeClass('active').html(selectResult);
                dropBlock.slideUp();
            });
        } else {
            $(this).removeClass('active');
            dropBlock.slideUp();
        }
        return false;
    });

    var mobile = $('.mobile-menu');
    var menu = $('.header-menu');

    $(mobile).on('click', function (e) {
        e.preventDefault();
        menu.toggleClass('opened');
        mobile.toggleClass('toggled-on');
    });

    $('.filter-price-input').click(function () {
        $(this).addClass('filter-border-color');
    });


    //  ативация SelectFx для кастомизации селектов
    (function () {
        [].slice.call(document.querySelectorAll('select.cs-select')).forEach(function (el) {
            new SelectFx(el, {
                onChange: function () {
                    var optText = $.trim($(el).closest('.js-sort').siblings('.cs-placeholder').text()),
                        option = $('.js-sort option:contains("' + optText + '")');
                    console.log(optText, option);
                    if (option && option.data('url'))
                        window.location.href = option.data('url');
                }
            });
        });
    })();

    //  слайдер услуг
    $('.slider-services__slider').slick({
        arrows: true,
        prevArrow: "<svg class='arrows arrows--prev' width='42' height='42' viewBox='0 0 477.175 477.175'><path d='M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z'/></svg>",
        nextArrow: "<svg class='arrows arrows--next' width='42' height='42' viewBox='0 0 477.175 477.175'><path d='M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z'/></svg>"
    });
    //  переключение активных картинок над слайдером
    var sliderServicesItem = $('.slider-services__slider__item'),
        sliderServicesImg = $('.slider-services__img__item');

    $('.arrows--prev').click(function () {
        var dataSlider = sliderServicesItem.filter('.slick-active').data('slider');
        sliderServicesImg.removeClass('active')
        sliderServicesImg.filter("[data-img='" + dataSlider + "']").addClass('active');
    });
    $('.arrows--next').click(function () {
        var dataSlider = sliderServicesItem.filter('.slick-active').data('slider');
        sliderServicesImg.removeClass('active')
        sliderServicesImg.filter("[data-img='" + dataSlider + "']").addClass('active');
    });

    //  слайдер риэлторов
    /*$('.realtors__carousel').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow: "<svg class='arrows arrows--prev' width='42' height='42' viewBox='0 0 477.175 477.175'><path d='M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z'/></svg>",
        nextArrow: "<svg class='arrows arrows--next' width='42' height='42' viewBox='0 0 477.175 477.175'><path d='M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z'/></svg>",
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });*/


    // слайдер фото квартиры
    $('.flat-details__slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.flat-details__slider_nav'
    });
    $('.flat-details__slider_nav').slick({
        slidesToShow: 5,
        slidesToScroll: 0,
        asNavFor: '.flat-details__slider',
        arrows: false,
        centerMode: true,
        focusOnSelect: true,
        vertical: true,
        responsive: [
            {
                breakpoint: 771,
                settings: {
                    vertical: false
                }
            },
            {
                breakpoint: 640,
                settings: {
                    vertical: false,
                    slidesToShow: 3,
                    slidesToScroll: 0
                }
            },
            {
                breakpoint: 500,
                settings: {
                    vertical: false,
                    slidesToShow: 2,
                    slidesToScroll: 0
                }
            },
            {
                breakpoint: 380,
                settings: {
                    vertical: false,
                    slidesToShow: 1,
                    slidesToScroll: 0
                }
            }
        ]
    });

    var navItem = $('.flat-details__slider_nav__item__wrap');
    navItem.click(function () {
        navItem.removeClass('active');
        $(this).addClass('active');
    });

    //  выделение активной кнопки таб панели и активация карты во вкладке
    var flatOnMapNav = $('.flat-on-map__nav__item');
    flatOnMapNav.click(function (e) {
        e.preventDefault();
        var thisNav = $(this);
        flatOnMapNav.removeClass('active');
        thisNav.addClass('active');
        var idActive = thisNav.attr('href');
        $('.flat-on-map__content').hide();
        $(idActive).show();
        initMap();
    }).trigger('click');

    //  выделение активной кнопки таб панели и активация карты во вкладке
    var newFlatsTabNav = $('.new-flats__tab-nav');
    newFlatsTabNav.click(function (e) {
        e.preventDefault();
        var thisTab = $(this);
        newFlatsTabNav.removeClass('active');
        thisTab.addClass('active');
        var idActive = thisTab.attr('href');
        $('.tab-pane').hide();
        $(idActive).show();
        initMapCatalog();
    });
    $('.new-flats__tab-nav.active').first().trigger('click');
});

//$(document).ready(function () {
//
//    //  слайдер риэлторов
//    $('.realtors__carousel').slick({
//        infinite: true,
//        slidesToShow: 3,
//        slidesToScroll: 3,
//        prevArrow: "<svg class='arrows arrows--prev' width='42' height='42' viewBox='0 0 477.175 477.175'><path d='M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z'/></svg>",
//        nextArrow: "<svg class='arrows arrows--next' width='42' height='42' viewBox='0 0 477.175 477.175'><path d='M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z'/></svg>"
//    });
//});


//  инициализация карт на странице catalog
function initMapCatalog() {

    var //Marker icons
        icons = {
            newFlat1: {
                name: 'newFlat1',
                icon: '/assets/img/icons/place-mark__1.png'
            },
            newFlat2: {
                name: 'newFlat2',
                icon: '/assets/img/icons/place-mark__2.png'
            },
            newFlat3: {
                name: 'newFlat3',
                icon: '/assets/img/icons/place-mark__3.png'
            }
        },
        //Block numbers with objects
        arQuantityRoom = ['one', 'two', 'three', 'four'];

    document.getElementById("tab-content-map").querySelectorAll(".tab-pane").forEach(
        function (panel, i) {

            if (panel.querySelector("#map-" + (i + 1)) !== null) {

                var features = [],
                    myLatLng = [],
                    bounds = new google.maps.LatLngBounds(),
                    check = true;

                document.getElementById(arQuantityRoom[i] + "-room").querySelectorAll(".map-coordinates").forEach(
                    function (coordinates, i) {

                        if (coordinates.dataset.lat == undefined || coordinates.dataset.lng == undefined) {
                            return
                        }

                        features[i] = {
                            position: new google.maps.LatLng(coordinates.dataset.lat, coordinates.dataset.lng),
                            type: 'newFlat' + (i + 1),
                            infowindow: new google.maps.InfoWindow({content: coordinates.dataset.address})
                        };

                        if (check) {
                            //Map center default
                            myLatLng = {
                                lat: Number(coordinates.dataset.lat),
                                lng: Number(coordinates.dataset.lng)
                            };
                            check = false;
                        }
                    }
                );
                if (features.length != 0) {
                    // Create a map object and specify the DOM element for display.
                    var map = new google.maps.Map(document.getElementById('map-' + (i + 1)), {
                        center: myLatLng,
                        zoom: 10,
                        zoomControl: false,
                        mapTypeControl: false,
                        scaleControl: false,
                        streetViewControl: false,
                        rotateControl: false,
                        fullscreenControl: false
                    });


                    // Create markers.
                    features.forEach(function (feature) {
                        var marker = new google.maps.Marker({
                            position: feature.position,
                            icon: icons[feature.type].icon,
                            map: map
                        });
                        if (features.length > 1) {
                            bounds.extend(feature.position);
                        }

                        if (feature.infowindow.content.length) {
                            // Additional description as an address
                            marker.addListener('click', function () {
                                feature.infowindow.open(map, marker);
                            });
                        }
                    });

                    if (features.length > 1) {
                        map.fitBounds(bounds);
                    }
                }
            }
        }
    );
}

//  инициализация карт на странице map
function initMap() {

    var map,
        myLatLng;

    myLatLng = {
        lat: 49.938829,
        lng: 30.697245
    };
    if (document.getElementById('map-location-flat') !== null) {
        var coords = $('#map-location-flat').data('coords');
        if (coords && (coords = coords.split(','))) {
            myLatLng = {
                lat: parseFloat(coords[0]),
                lng: parseFloat(coords[1])
            }
        }
        // Create a map object and specify the DOM element for display.
        map = new google.maps.Map(document.getElementById('map-location-flat'), {
            center: myLatLng,
            zoom: 15,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false
        });

        var icons = {
            newFlat: {
                name: 'newFlat',
                icon: '/assets/img/icons/map-location-flat.png'
            }
        };

        var features = [
            {
                position: new google.maps.LatLng(myLatLng.lat, myLatLng.lng),
                type: 'newFlat'
            }
        ];

        // Create markers.
        features.forEach(function (feature) {
            var marker = new google.maps.Marker({
                position: feature.position,
                icon: icons[feature.type].icon,
                map: map
            });
        });
    }
}

function initialize() {

    var map; //<-- This is now available to both event listeners and the initialize() function

    var greyScaleMapType = new google.maps.StyledMapType(
        [{
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [{
                "color": "#e9e9e9"
            }, {
                "lightness": 17
            }]
        },
            {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }, {
                    "lightness": 20
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 17
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 29
                }, {
                    "weight": 0.2
                }]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 18
                }]
            },
            {
                "featureType": "road.local",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 16
                }]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }, {
                    "lightness": 21
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dedede"
                }, {
                    "lightness": 21
                }]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "on"
                }, {
                    "color": "#ffffff"
                }, {
                    "lightness": 16
                }]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "saturation": 36
                }, {
                    "color": "#333333"
                }, {
                    "lightness": 40
                }]
            },
            {
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            },
            {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f2f2f2"
                }, {
                    "lightness": 19
                }]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#fefefe"
                }, {
                    "lightness": 20
                }]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#fefefe"
                }, {
                    "lightness": 17
                }, {
                    "weight": 1.2
                }]
            }], {
            name: 'greyScale'
        });

    var greyScaleMapTypeId = 'grey_Scale';

    var myLatLng = {
        lat: 55.748700,
        lng: 37.622860
    };
    var mapOptions = {
        center: myLatLng,
        zoom: 10,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, greyScaleMapTypeId]
        },
        disableDefaultUI: true

    };
    map = new google.maps.Map(document.getElementById('map_contacts'), mapOptions);
    map.mapTypes.set(greyScaleMapTypeId, greyScaleMapType);
    map.setMapTypeId(greyScaleMapTypeId);

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        icon: '/assets/img/icons/map-marker.png'
    });

    if (document.getElementById('map_contacts') !== null) {
        google.maps.event.addDomListener(window, 'load', initialize);
        google.maps.event.addDomListener(window, "resize", function () {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });
    }
}