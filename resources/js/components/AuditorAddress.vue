<template>
    <div class="uk-width-1-1 uk-margin-small-top">
        <div class="address" >
            <i class="a-mailbox"></i> <span v-html="address.address"></span> <i class="uk-icon-trash use-hand-cursor" style="margin-left:10px;" v-on:click="removeAddress" uk-tooltip="CLICK TO DELETE THIS ADDRESS"></i>
        </div>
    </div>
</template>

<script>
  export default {
    props: ['address','index'],

    methods: {

         removeAddress: function() {
            axios.post('/auditoraddresses/'+this.address.address_id+'/delete').then(response => {
              this.$emit('address-remove', this.index);
            });
            
        }
    }
  };
</script>