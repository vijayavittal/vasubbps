<template>
    <agent-layout>
        <div class="flex flex-col">
            <div class="flex flex-row mx-8 justify-between">
                <div class="w-1/4 bg-secondary mr-4 rounded rounded-md p-5 flex flex-col justify-center">
                    <p class="font-medium font-body text-2xl text-center text-primary mb-10">Menu</p>
                    <p :class="selectedItem === 'DTH'?'bg-primary text-secondary' : 'text-primary'"  v-on:click="menuClicked('DTH')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">DTH</p>
                    <p :class="selectedItem === 'Water'?'bg-primary text-secondary' : 'text-primary'"  v-on:click="menuClicked('Water')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Water</p>
                    <p :class="selectedItem === 'Mobile (Prepaid)'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Mobile (Prepaid)')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Mobile (Prepaid)</p>
                    <p :class="selectedItem === 'Mobile (Postpaid)'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Mobile (Postpaid)')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Mobile (Postpaid)</p>
                    <p :class="selectedItem === 'Landline'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Landline')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Landline</p>
                    <p :class="selectedItem === 'Broadband'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Broadband')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Broadband</p>
                    <p :class="selectedItem === 'Electricity'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Electricity')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Electricity</p>
                    <p :class="selectedItem === 'FastTag'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('FastTag')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">FastTag</p>
                    <p :class="selectedItem === 'Gas'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Gas')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Gas</p>
                    <p :class="selectedItem === 'Life Insurance'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Life Insurance')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Life Insurance</p>
                    <p :class="selectedItem === 'Cable Tv'?'bg-primary text-secondary' : 'text-primary'" v-on:click="menuClicked('Cable Tv')" class="self-center cursor-pointer font-medium font-body text-md  rounded rounded-md w-1/2 my-2 py-3 text-center">Cable Tv</p>
                </div>
                <div class="w-3/4 bg-secondary rounded rounded-md p-5">
                    <component :key="componentKey" :type="selectedItem" v-on:geoService="updateGeoService" v-on:rdService="updateRdService" :is="selectedComponent"></component>
                </div>
            </div>
            <div class="mx-12 mt-5">
                <div class="flex flex-row align-middle justify-end">
                    <p class="mr-10 font-body text-primary font-medium">RD Service</p>
                    <img class="w-4 h-4 my-auto justify-center align-middle" :src="rdService.detected ? 'https://i.ibb.co/dJc3wvs/success.png':'https://i.ibb.co/Gxsh2rq/failure.png'">
                </div>
                <div class="flex flex-row align-middle mt-3 justify-end">
                    <p class="mr-5 font-body text-primary font-medium">GEO Location</p>
                    <img class="w-4 h-4 my-auto justify-center align-middle" :src="location.detected ? 'https://i.ibb.co/dJc3wvs/success.png':'https://i.ibb.co/Gxsh2rq/failure.png'">
                </div>
            </div>
        </div>
    </agent-layout>
</template>

<script>
import AgentLayout from "../Shared/AgentLayout";
import axios from "axios";
import Vue from "vue";
import * as vm from "vm";
export default {
    name: "Portal",
    components: {AgentLayout},
    data:function (){
        return{
            selectedItem:'matm',
            location:{
                detected:false,
            },
            rdService:{
                detected:false
            },
            selectedComponent:null,
            componentKey:1,

        }
    },
    mounted() {


        // Vue.component(
        //     'atm',
        //     // A dynamic import returns a Promise.
        //     () => import('../Shared/ATM'),
        //     {

        //     }


        // );
        Vue.component(
            'bbps',
            // A dynamic import returns a Promise.
            () => import('../Shared/BBPS'),

        );

        this.selectedComponent='atm';


    },
    methods:{
        menuClicked(item){
            if(item === 'matm' && this.selectedItem !== 'matm'){
                this.selectedComponent='atm'
            }else{
                this.selectedComponent='bbps'
            }

            this.selectedItem = item;

            this.componentKey++;
        },
        updateGeoService(value){
            this.location.detected = value;
        },
        updateRdService(value){
            this.rdService.detected = value;
        }
    }
}
</script>

<style scoped>

</style>
