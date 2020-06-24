<template>
    <card class="flex flex-col items-center justify-center">
        <div class="px-3 py-3">
            <h3 class="text-center text-2xl text-80 font-light">Add product</h3>
            <p>&nbsp;</p>
            <form>
                <label for="ali-product-id">Ali ID:</label>
                <input v-model="id" type="text" id="ali-product-id" style="border: 1px #808080 solid; width: 50%;" />
                <input @click="sendAliID" type="submit" value="Add (find)" style="border: 1px #808080 solid; cursor: pointer;" />
            </form>
        </div>
    </card>
</template>

<script>
import axios from 'axios';
export default {
    props: [
        'card',
    ],
    data() {
        return {
            id: ''
        }
    },
    methods: {
        sendAliID(e) {
            e.preventDefault();
            axios.get('/nova-dropwow/add-product/ali', {params: {id: this.id}}).then((data) => {
                if (data.data.hasOwnProperty('redirect')) {
                    document.location.href = data.data.redirect;
                } else if (data.data.hasOwnProperty('message')) {
                    alert(data.data.message);
                }
                this.id = '';
            }).catch((data) => {
                alert(data.response.data.message);
                this.id = '';
            });
        }
    },
    mounted() {
        //
    },
}
</script>
