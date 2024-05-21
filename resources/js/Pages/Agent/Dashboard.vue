<template>
    <agent-layout>
        <div>
            <p>token:{{token}}</p>
            <div class="flex-row flex justify-center mt-4 mx-8">
                <p class="text-primary font-body font-medium text-2xl">Today's Analysis</p>
                <select v-if="$page.props.auth.user.pan === 'XXXXXXXXXX'" v-model="userSelected" class="bg-secondary px-6 py-3 mx-4 self-center rounded-lg text-primary font-body" v-on:change="userSelectedChanged">
                    <option value="" selected disabled>Please Select</option>
                    <option v-for="user in users" :value="user.pan" >{{user.name}}</option>
                </select>
            </div>
            <div class="flex flex-row flex-wrap justify-center my-4 mx-8  overflow-x-auto">

                <div v-for="t in top" class="bg-primary w-64 flex flex-col justify-between p-4 rounded-lg mr-6">
                    <p class="self-start text-secondary font-body font-thin text-xl">{{t.name}}</p>
                    <p class="self-end text-secondary font-body font-medium text-5xl">&#8377; {{t.amount}}</p>
                </div>

            </div>
            <div class="w-5/6 bg-primary mx-auto mt-12 rounded-lg relative">
                <div class="flex-row flex justify-between">
                    <p class="absolute top-0 ml-8 mt-8  self-start text-secondary font-body font-thin text-2xl">Today's Total</p>
                    <p class="absolute top-0 right-0 mr-8 mt-8  self-start text-secondary font-body font-medium text-4xl">&#8377; {{amount}}</p>
                </div>
                <canvas class="w-3/4 self-center px-8 pt-16" id="myChart" ></canvas>
            </div>

        </div>
    </agent-layout>

</template>

<script>
import AgentLayout from "../Shared/AgentLayout";
import Chart from 'chart.js'
export default {
    name: "Dashboard",
    components: {AgentLayout},
    props:['total','top','amount','users','token'],
    data:function (){
        return {
            userSelected:'',
        }
    },
    mounted() {
        var ctx = document.getElementById('myChart').getContext("2d");
        var labels1 = [];
        var values = [];

        for(let [k,v] of this.total){
            labels1.push(k);
            values.push(v);
        }
        var gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(77,71,58,1)');
        gradient.addColorStop(1, 'rgba(175,72,57,1)');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels1,
                datasets: [{
                    label: 'Total Sales Value',
                    data: values,
                    fillColor:'rgba(255, 255, 255, 0.8)',
                    backgroundColor:'rgba(255, 255, 255, 0.8)',
                    borderWidth: 1,
                    pointBackgroundColor:'rgba(255, 255, 255, 0.8)',
                    pointHoverRadius:10,
                    pointRadius:8,
                }]
            },
            options: {
                legend: {
                    labels: {
                        fontColor: "white",
                        fontSize: 18
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        type: 'time',
                        time :{
                            unit: 'day'
                        },
                        ticks:{
                            fontColor:'white',
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            fontColor:'white',
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    },
    methods:{
        userSelectedChanged(){

        }
    }
}
</script>

<style scoped>

</style>
