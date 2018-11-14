<template>
<div>
	<div class="row">
        <div class="col-md-3">
            <form @submit.prevent="addCustomer" class="form-horizontal">
                    <div class="form-group">
                        <label for="customerNameid" class="control-label">New customer</label>
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" v-model="customer.customername" class="form-control" placeholder="New Customer">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success">Add customer</button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
        <div class="col-md-1">
            <div class="clear-fix"></div>
        </div>
        <div class="col-md-8">
			<form @submit.prevent="addTransaction">      
                <div class="form-group">
                    <label for="amountId" class="control-label"> New / Edit transaction</label>
                    <div class="row">
                        <div class="col-md-4">
                            <!-- <select class="form-control"> -->
							<select class="form-control"  v-model="transaction.customerId">
                                <option value="0" >Select customer...</option>
								<option v-for="customer in customers" v-bind:key="customer.id" v-bind:value="customer.id">
									{{ customer.customername }}
								</option>
                            </select>
                        </div>					
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="0.00" v-model="transaction.amount">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success" style="width:80%">Save</button>
                        </div>
                    </div>
                </div>
			</form>
        </div>
    </div>

   <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    Transactions. Search :
                        <input class="text-center ma-2" type="text" @blur="searchTransactions()" v-model="search" placeholder="amount or name">
                            <a href="/" class="btn">Reset filter</a>
                </div>

            </div>
        </div>
    </div>
        <div class="table table-responsive"><br/>
            <table id="tblid" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">ID</th>
                        <th width="20%" class="text-center">Customer</th>
                        <th width="15%" class="text-center">Amount</th>
                        <th width="20%" class="text-center">Updated</th>
                        <th width="10%" class="text-center">Edit</th>
                        <th width="10%" class="text-center">Delete</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="transaction in transactions" v-bind:key="transaction.id">
                	<td class="table-text">{{ transaction.id }}</td>
					<td class="table-text">{{ transaction.customer }}</td>
					<td class="table-text">{{ transaction.amount }}</td>
					<td class="table-text">{{ transaction.updated_at }}</td>
					<td class="table-text">
                        <button class="btn btn-info" style="width:80%"
						@click="editTransaction(transaction)"
						>Edit</button>
					</td>
					<td class="table-text">
                        <button class="btn btn-danger" style="width:80%"
                        @click="deleteTransaction(transaction.id)"
                        >Delete</button>
					</td>		
                </tr>
                </tbody>
            </table>
        </div>
        <nav aria-label="Go to page">
		  <ul class="pagination">
		    <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item"><a class="page-link" href="#"
		    @click="fetchTransactions(pagination.prev_page_url)"
		    >Prev</a></li>

		    <li class="page-item"><a class="page-link text-dark" href="#">Page {{pagination.current_page}} of {{pagination.last_page}} </a></li>

		    <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item"><a class="page-link" href="#" @click="fetchTransactions(pagination.next_page_url)">Next</a></li>
		  </ul>
		</nav>
</div>
</template>
<script>
	export default{
		data(){
			return{
				search: '',
				customers:[],
				customer:{
					id: '',
					customername: ''
				},
				transactions:[],
				transaction:{
					id: '',
					customerId: '',
					customer: '',
					amount: '',
					updated_at: ''
				},
				transaction_id: '',
				pagination: {},
				edit: false
			}
		},

		created(){
			this.fetchTransactions();
			this.fetchCustomers();
		},

		methods: {

			fetchCustomers(){
				let page_url = 'api/customers';
				fetch(page_url)
					.then(res => res.json())
					.then(res =>{
						this.customers = res.data;
					})
					.catch(err => console.log(err));
			},

			fetchTransactions(page_url){
			let vm = this;
			page_url = page_url || 'api/transactions'
				fetch(page_url)
					.then(res => res.json())
					.then(res =>{
						this.transactions = res.data;
						vm.makePagination(res.meta, res.links);
					})
				.catch(err => console.log(err));
			},

			searchTransactions(){
				let val = this.search;
				let vm = this;
				fetch(`api/search/${val}`, {
						method: 'get'
					})
					.then(res => res.json())
					.then(res =>{
						this.transactions = res.data;
						vm.makePagination(res.meta, res.links);
					})
					.catch(err => console.log(err));
			},

			makePagination(meta, links){
				let pagination = {
					current_page: meta.current_page,
					last_page: meta.last_page,
					next_page_url: links.next,
					prev_page_url: links.prev
				}
				this.pagination = pagination;
			},

			deleteTransaction(id){
				if(confirm('Are you sure to delete record ' + id + '?')){
					fetch(`api/transaction/${id}`, {
						method: 'delete'
					})
					.then(res => res.json())
					.then(data => {
						alert('Transaction deleted.');
						this.fetchTransactions();
					})
					.catch(err => console.log(err));
				}
			},

			addTransaction(){
				if(this.edit === false){
					fetch('api/transaction',{
						method: 'post',
						body: JSON.stringify(this.transaction),
						headers: {
							'content-type': 'application/json'
						}
					})
					.then(res => res.json())
					.then(data => {
						this.transaction.customerId = '';
						this.transaction.amount = '';
						alert('Transaction added');
						this.fetchTransactions();
					})
					.catch(err => console.log(err))
				}
				else{
					fetch('api/transaction',{
						method: 'put',
						body: JSON.stringify(this.transaction),
						headers: {
							'content-type': 'application/json'
						}
					})
					.then(
						res => res.json()
					)
					.then(data => {
						this.transaction.customerId = '';
						this.transaction.amount = '';
						alert('Transaction updated');
						this.fetchTransactions();
					})
					.catch(err => console.log(err))
				}
			},

			editTransaction(transaction){
				this.edit = true;
				this.transaction.id = transaction.id;
				this.transaction.transaction_id = transaction.id;
				this.transaction.customerId = transaction.customerId;
				this.transaction.customer = transaction.customer;
				this.transaction.amount = transaction.amount;
				//this.transaction.customerId = transaction.customerId;
			},

			addCustomer(){

					fetch('api/customer',{
						method: 'post',
						body: JSON.stringify(this.customer),
						headers: {
							'content-type': 'application/json'
						}
					})
					.then(res => res.json())
					.then(data => {
						this.customer.customername = '';
						alert('Customer added');
						this.fetchTransactions();
						this.fetchCustomers();
					})
					.catch(err => console.log(err))
			}

		}
	}
</script>