<template>
    <div class="bg-fixed w-11/12 mx-auto mb-20" >
        <div class="mx-10 mt-5 mb-4 flex flex-row justify-between">
            <div class="ml-3 w-32 flex flex-row justify-center">
                <img class="w-16 mx-4" src="../../../images/logo.png">
                <img class="w-32 self-center" src="../../../images/KADAMBA.png">
            </div>
            <div class=" flex flex-row justify-center items-center">
                <Link class="bg-secondary px-6 py-3 mx-2 rounded-lg text-primary p-0 font-body" href="/dashboard">HOME</Link>
                <Link class="bg-secondary px-6 py-3 mx-2 rounded-lg text-primary p-0 font-body" href="/pricings">PRICING</Link>
                <form class="flex flex-row items-center my-auto" id="portalForm">
                    <a href="/transactions" class="bg-secondary px-6 py-3 mx-2 rounded-lg text-primary p-0 font-body"> TRANSACTIONS</a>
                    <a v-if="$page.props.auth.user.pan !== 'XXXXXXXXXX' "  href="/integration" class="bg-secondary px-6 py-3 mx-2 rounded-lg text-primary p-0 font-body"> INTGRATION</a>
               
                    <Link  v-if="$page.props.auth.user.pan !== 'XXXXXXXXXX' "class="bg-secondary px-6 py-3 mx-2 rounded-lg text-primary p-0 font-body" href="/ShowTokens">VIEW TOKEN</Link>
                    <Link  v-if="$page.props.auth.user.pan == 'XXXXXXXXXX' " class="bg-secondary px-6 py-3 mx-2 rounded-lg text-primary p-0 font-body" href="/LoadBalance">LOAD BALANCE</Link>
                    <Link v-if="$page.props.auth.user.pan !== 'XXXXXXXXXX' " href="/portal"  class="bg-secondary px-6 py-3 mx-2 self-center rounded-lg text-primary font-body"> PORTAL</Link>
                </form>
                <form method="POST" action="/logout" class="flex flex-row items-center my-auto">
                    <button type="submit" class="bg-primary px-6 py-3 mx-2 rounded-lg text-secondary font-body"> LOGOUT</button>
                </form>
            </div>
        </div>
        <div class="flex-row flex justify-between mx-8 my-4">
            <p class="text-primary font-body font-medium text-2xl">{{$page.props.auth.user.name}}</p>
            <p v-on:click="refreshBalance" class="text-primary font-body font-medium text-2xl cursor-pointer" >Balance : {{balance}} 🔄 </p>
        </div>
        <slot></slot>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue'
import axios from "axios";
export default {
    name: "AgentLayout",
    components:{Link},
    data:function (){
        return {
            lat:null,
            long:null,
            port:null,
            balance:0,
        }
    },
    mounted() {
        this.balance = this.$page.props.auth.user.balance;
    },
    methods:{
        refreshBalance(){
            axios.post('./web/balance').then(response=>{
               if (response.data.response_code === 'TXN'){
                   this.balance = response.data.transactions;
                   alert("Balance successfully loaded");
               }else{
                   alert("Error. PLease try after some time")
               }
            });
        }
    }
}
</script>

<style scoped>

</style>
