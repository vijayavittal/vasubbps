<template>
<agent-layout>
    <div class="my-4  justify-center flex flex-col">
        <p class="font-body text-primary text-xl self-center font-bold">Pricing Table</p>
        <select class="w-1/4 my-4" v-on:change="billerChanged" v-model="biller" >
            <option value="" selected disabled>Select Biller Category</option>
            <option v-for="(biller,i) in billers" :key="i" :value="biller.type" >{{biller.type}}</option>
        </select>
    </div>
    <table class="w-full mt-8 ">
        <thead>
        <tr>
            <th class="text-center text-primary font-bold py-3">ID</th>
            <th class="text-center text-primary font-bold">Category</th>
            <th class="text-center text-primary font-bold">Product Name</th>
            <th class="text-center text-primary font-bold">Product Code</th>
            <th class="text-center text-primary font-bold">Commission Type</th>
            <th v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class="text-center text-primary font-bold">Total Comm</th>
            <th v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class="text-center text-primary font-bold">Kadamba Comm</th>
            <th class="text-center text-primary font-bold">Agent Comm</th>
            <th class="text-center text-primary font-bold">Date</th>
        </tr>
        </thead>
        <tbody class="text-center">
        <tr v-for="transaction in transactions.data" :key="transaction.id" class=" my-1 py-2 border-2 border-primary rounded rounded-md">
            <td class="text-primary py-5">{{transaction.id}}</td>
            <td class=" text-primary">{{transaction.type}}</td>
            <td class=" text-primary">{{transaction.name}}</td>
            <td class=" text-primary">{{transaction.sp_key}}</td>
            <td class=" text-primary">{{transaction.commission_type === 'F' ? 'Fixed' : 'Percent'}}</td>
            <td v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class=" text-primary">{{transaction.commission}}</td>
            <td v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class=" text-primary">{{transaction.k_commission}}</td>
            <td class=" text-primary">{{transaction.a_commission}}</td>
            <td class=" text-primary">{{transaction.create}}</td>
        </tr>
        </tbody>
    </table>
    <div class="my-4" v-if="transactions.links.length > 3">
        <div class="flex flex-row flex-wrap -mb-1">
            <div v-for="(link, key) in transactions.links">
                <Link v-if="link.url === null" :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                <Link v-else :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-primary text-white': link.active }" :href="link.url" v-html="link.label" />
            </div>
        </div>
    </div>
</agent-layout>
</template>

<script>
import AgentLayout from "../Shared/AgentLayout";
export default {
    name: "Pricings",
    props:['transactions','billers'],
    components: {AgentLayout},
    data:function (){
        return{
            biller:'',
        }
    },
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('type')){
            console.log(urlParams.get('type'))
            this.biller = urlParams.get('type');
        }

    },
    methods:{
        billerChanged(){
            window.location.href = '/pricings?type='+this.biller;
        },
    }
}
</script>

<style scoped>

</style>
