<template>
    <agent-layout>
        
           
    
        <table class="w-full mt-8 ">
        <thead>
        <tr>
            <th class="text-center text-primary font-bold py-3">ID</th>
            <th class="text-center text-primary font-bold">Token name</th>
            <th class="text-center text-primary font-bold">Token Expiry</th>
           
        </tr>
        </thead>
        <tbody class="text-center">
        <tr  class=" my-1 py-2 border-2 border-primary rounded rounded-md">
            <td class="text-primary py-5">{{token.id}}</td>
            <td class=" text-primary">{{token.name}}</td>
            <td class=" text-primary">{{token.expires}}</td>
            
        </tr>
        </tbody>
    </table>
    </agent-layout>
    
    </template>
    <script>
    import AgentLayout from "../Shared/AgentLayout";
   
    export default {
        name: "ViewIntegration",
        props:['token'],
        components: {AgentLayout},
        data:function (){
           
            return {
              
            }
        },
        
       
    }
    </script>
    
    <style scoped>
    
    </style>