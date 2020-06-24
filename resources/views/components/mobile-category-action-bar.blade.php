<mobile-categories-action-bar
    :ship_country="ship_country"
    :minPrice="sliderValues[0]"
    :maxPrice="sliderValues[1]"
    :categories="{{json_encode($categories->toArray())}}"

/>