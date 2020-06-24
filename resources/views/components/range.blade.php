<form method="get" action="{{ route('category.products', ['category'=>$category]) }}" class="range">
    <div class="range__ships_from">
        <h3 class="ships_from__title">Ships from:</h3>
        <dropdown
                :default_val="'All Countries'"
                :ssr_value="ship_country"
                :ssr_name="'ship_country'"
                :ssr="true"
                :options="[{id: 'usa', value: 'USA'},{id: 'china', value: 'China'}]"
        />
    </div>
    <h3 class="range__title">Price</h3>
    <div class="slider-container">
        <slider
                v-model="sliderValues"
                v-bind="sliderOptions"
        />
    </div>

    <div class="range__period">
        <span class="range__period_item">
            <div class="text-field">
                <div class="dollar-sign">
                    $
                </div>
                <input
                        name="pmin"
                        @input="e=>onPriceInputChange(e.target.value, 0)"
                        type="number"
                        class="text-field__input-money"
                        v-model="sliderValues[0]"
                        ref="min">
            </div>
        </span>
        <span class="range__period_item">
            <div class="text-field">
                <div class="dollar-sign">
                    $
                </div>
                 <input
                         name="pmax"
                         @input="e=>onPriceInputChange(e.target.value, 1)"
                         type="number"
                         class="text-field__input-money"
                         v-model="sliderValues[1]"
                         ref="max">
            </div>
        </span>
    </div>

    <label class="btn btn-wide btn-small">
        Submit <input hidden type="submit"/>
    </label>
</form>


