<template>
  <div class="h-3 ml-5">
        <h1 class="text-primary self-center font-body font-bold text-xl text-center">Load Money</h1>
       
        <div  class="flex flex-row justify-start h-full">
            <div class="w-1/2">
                <p class="text-primary  font-medium  font-body text-md py-2">Select the Agent: </p>
                <select  class="w-full appearance-none text-primary my-2 bg-secondary rounded rounded-md py-2 px-2  "  v-model="Agentid" name="Agentid">
                    <option value="">Agent List</option>
                    <option  v-for="agent in agents"  :key="agent.pan" :value="agent.pan">{{agent.name}}</option>
                </select>
                
                  <p-input v-model="Amount"  type="number" min="1" name="Amount"/>
                  <button type="submit" class="mt-8 px-4 py-3 bg-red-800 text-white w-2/6 self-center" v-on:click="submitdetails">Submit</button>
            </div>
         
        </div>
    </div>

    
    </template>
    <script>

    import PInput from "../Shared/P-Input";
    export default {
        name: "LoadBalance",
        props:['agents'],
        components: {PInput},
        data:function (){
           
            return {
                Agentid:null,
                Amount:null
            }
        },
        methods:{
       
        submitdetails(){
            this.$inertia.post('/post-balance',{
             'Agentid':this.Agentid,
             'Amount':this.Amount,

            })
        },
       
    }
       
    }
    </script>
    
    <style scoped>
    
    </style>