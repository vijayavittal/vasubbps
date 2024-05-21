<template>
<agent-layout>
    
        <input v-model="name" name="name"  required class="self-center mt-12 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl " type="text" placeholder="Name">
       
<button type="submit" class="mt-8 px-4 py-3 bg-red-800 text-white w-2/6 self-center" v-on:click="submitdetails">Integration</button><br>

<!-- <div class="p-2 bg-green-200 border-2 border-green-400 rounded-md mt-6 overflow-y-auto" v-if="token">
    <p class="text-gray-700">{{ token }}</p>
</div> -->

<div v-if="token">
    <div>
        <input v-model="token" name="name" id="example" ref="message" required class="self-center mt-12 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl overflow-y-auto" type="text" >
     
    </div>
    
    <button type="submit" class="mt-8 px-4 py-3 bg-red-800 text-white w-2/6 self-center" v-on:click="copyText">Click to copy</button>
  </div>
 
</agent-layout>

</template>
<script>
import AgentLayout from "../Shared/AgentLayout";
import daterangepicker from "daterangepicker"
import axios from "axios";

import { Link } from '@inertiajs/inertia-vue'
export default {
    name: "Integration",
    props:['token'],
    components: {AgentLayout,Link},
    data: () => ({ message: '' }),
    data:function (){
      
        return {
            userSelect:'',
           
        }
    },
    
    methods:{
        copyText() {
      const element = this.$refs.message;
      element.select();
      element.setSelectionRange(0, 99999);
      document.execCommand('copy');
    },
        submitdetails(){
            this.$inertia.post('/create-jwt',{
             'name':this.name,

            })
        },
       
    }
}
</script>

<style scoped>

</style>