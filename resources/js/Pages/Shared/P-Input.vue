<template>
<div class="w-full flex flex-col my-1">
    <p class="font-body text-primary">{{name}}</p>
    <div class="rounded rounded-md flex flex-row" :class="(inputData.length >= min && inputData.length <= max) ? 'border-green-500 border-2' :'border-primary border-2' ">
        <input v-on:input="inputChanged"  class="w-full focus:outline-none appearance-none text-primary bg-secondary  py-2 px-2" v-model="inputData"/>
        <img v-if="validated" class="w-6 h-6 align-middle my-auto mx-1" src="../../../images/success_transaction.png">
    </div>

</div>
</template>

<script>
export default {
    name: "P-Input",
    props:['min','name','type','max','index'],
    data:function (){
        return {
            inputData:'',
            validated:false,
        }
    },
    methods:{
        inputChanged:function (event){
            if (this.inputData.length >= this.min && this.inputData.length <= this.max){
                this.validated = true;
                this.$emit('input', event.target.value,true,this.index);
            }else{
                this.validated = false;
                this.$emit('input', event.target.value,false,this.index);
            }

        }
    }
}
</script>

<style scoped>

</style>
