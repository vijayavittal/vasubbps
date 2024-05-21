<template>
    <agent-layout>
        <div class="w-1/2">
                <!-- <div  class="flex flex-col overflow-y-scroll" v-if="success">
                    <img class="w-32 self-center mb-2" src="../../../images/success_transaction.png">
                    <p class="font-body text-primary font-medium self-center my-2">Transaction Successful</p>
                </div> -->
                <div  class="flex flex-col justify-center my-auto">
                    <table class="table-fixed">
 
  <tbody>
    <tr>
      <td class="border px-4 py-2">Biller Name</td>
      <td class="border px-4 py-2">{{name}}</td>
     
    </tr>
    <tr class="bg-gray-100">
      <td class="border px-4 py-2">Due Date</td>
      <td class="border px-4 py-2">{{duedate}}</td>
      
    </tr>
    <tr>
      <td class="border px-4 py-2">Amount</td>
      <td class="border px-4 py-2">Rs {{amount}}</td>
     
    </tr>
    <tr>
      <td class="border px-4 py-2">Pay</td>
      <td class="border px-4 py-2"> <button  class="w-full rounded rounded-md text-white py-2 my-2 bg-red-800">Pay</button></td>
     
    </tr>
  </tbody>
</table>
                    <!-- <p class="text-primary self-center font-body font-bold text-xl text-center my-1">Bill Name</p>
                    <p class="text-primary self-center font-body font-medium text-md text-center mb-3 mt-1"> {{name}} </p>
                 <p class="text-primary self-center font-body font-bold text-md text-center my-1">Due Date</p>
                    <p class="text-primary self-center font-body font-medium text-md text-center mb-3 mt-1">{{duedate}}</p> 
                    <p class="text-primary self-center font-body font-bold text-md text-center my-1">Amount</p>
                    <p class="text-primary self-center font-body font-medium text-md text-center mb-3 mt-1">Rs {{amount}}</p>  -->
                   
                </div>
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
        props:['name','amount','duedate'],
        components: {AgentLayout,Link},
        data: () => ({ message: '' }),
        data:function (){
          
            
        },
        
        methods:{
            
          
           
        }
    }
    </script>
    
    <style scoped>
    
    </style>