<template>
<agent-layout>
    <div class="flex flex-row justify-center items-center">
        <p class="text-primary p-0 mr-6 font-body self-center">Total Agent Commission : <span class="font-bold"> {{agentTotal}}</span></p>
        <input class="border border-primary bg-white rounded-lg text-primary text-center p-2" type="text" name="datetimes" />
        <select v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" v-model="userSelect" class="bg-secondary px-6 py-3 mx-4 self-center rounded-lg text-primary font-body" v-on:change="userSelectedChanged">
            <option value="" selected disabled>Please Select</option>
            <option v-for="user in users" :value="user.pan" >{{user.name}}</option>
        </select>
        <p class="text-primary p-0 mr-6 font-body self-center">Total Kadamba Commission : <span class="font-bold"> {{kadambaTotal}}</span></p>
    </div>
    <table class="w-full mt-8 ">
        <thead>
        <tr>
            <th class="text-center text-primary font-bold py-3">ID</th>
            <th class="text-center text-primary font-bold">Type</th>
            <th class="text-center text-primary font-bold">Phone</th>
            <th class="text-center text-primary font-bold">Amount</th>
            <th v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class="text-center text-primary font-bold">User</th>
            <th v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class="text-center text-primary font-bold">Kadamba TDS</th>
            <th v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class="text-center text-primary font-bold">Kadamba Comm</th>
            <th class="text-center text-primary font-bold">Agent Comm</th>
            <th class="text-center text-primary font-bold">Agent TDS</th>
            <th class="text-center text-primary font-bold">Date</th>

        </tr>
        </thead>
        <tbody class="text-center">
        <tr v-for="transaction in transactions.data" :key="transaction.id" class=" my-1 py-2 border-2 border-primary rounded rounded-md">
            <td class="text-primary py-5">{{transaction.id}}</td>
            <td class=" text-primary">{{transaction.type}}</td>
            <td class=" text-primary">{{transaction.customer_params}}</td>
            <td class=" text-primary">Rs {{transaction.amount}}</td>
            <td v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class=" text-primary">{{transaction.user}}</td>
            <td v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class=" text-primary">{{transaction.total_tds}}</td>
            <td v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" class=" text-primary">{{transaction.kadamba_comm}}</td>
            <td class=" text-primary">{{transaction.agent_comm}}</td>
            <td class=" text-primary">{{transaction.agent_tds}}</td>
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
import daterangepicker from "daterangepicker"

import { Link } from '@inertiajs/inertia-vue'
export default {
    name: "Transactions",
    props:['transactions','links','agentTotal','kadambaTotal','users'],
    components: {AgentLayout,Link},
    data:function (){
        return {
            userSelect:''
        }
    },
    mounted() {
        console.log(this.transactions)
        const urlParams = new URLSearchParams(window.location.search);

        let from = new Date();
        let to = new Date();

        if(urlParams.has('from')){
            from = new Date(urlParams.get('from'))
        }
        if (urlParams.has('to')){
            to = new Date(urlParams.get('to'));
        }


        $(function() {

            $('input[name="datetimes"]').daterangepicker({
                maxDate: new Date(),
                startDate: from,
                endDate: to,
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        });

        $('input[name="datetimes"]').on('apply.daterangepicker',this.dateChanged);
    },
    methods:{
        userSelectedChanged(){
            const urlParams = new URLSearchParams(window.location.search);
            if(this.userSelect === 'XXXXXXXXXX'){
                window.location.href = '/transactions'
            }else{
                if(urlParams.has('from') && urlParams.has('to') ){
                    window.location.href = '/transactions?from='+urlParams.get('from')+'&to='+urlParams.get('to')+'&pan='+this.userSelect;
                }else{
                    window.location.href = '/transactions?pan='+this.userSelect;
                }
            }
        },
        dateChanged:function(ev, picker) {
            console.log(picker);
            let pan =  this.userSelect === 'XXXXXXXXXX' ? 'XXXXXXXXXX' : this.userSelect;
            window.location.href = '/transactions?from='+picker.startDate.format('YYYY/MM/DD')+'&to='+picker.endDate.format('YYYY/MM/DD')+'&pan='+pan;
        }
    }
}
</script>

<style scoped>

</style>
