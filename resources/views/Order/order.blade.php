@extends('layouts.admin')

@section('title', 'Order Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="projects">
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.orders') }}</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form method="GET" action="{{ route('orders.index') }}" style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                        <select name="per_page" class="per-page-select">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    @if(Auth::user()->role === 'Admin')
                    <button style="cursor: pointer;" onclick="createOrder()">{{ __('messages.add_order') }} <i class="fas fa-plus"></i></button>
                    @endif
                </div>
            </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>{{ __('messages.customer') }}</td>
                                <td>{{ __('messages.total_amount') }}</td>
                                <td>{{ __('messages.status') }}</td>
                                <td>{{ __('messages.payment_method') }}</td>
                                <td>{{ __('messages.date') }}</td>
                                @if(Auth::user()->role === 'Admin')
                                <td>{{ __('messages.action') }}</td>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Unknown' }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="status {{ strtolower($order->status) == 'completed' ? 'pink' : (strtolower($order->status) == 'pending' ? 'orange' : (strtolower($order->status) == 'cancelled' ? 'red' : 'red')) }}"></span> {{ $order->status }}
                                </td>
                                <td>{{ $order->payment_method }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                @if(Auth::user()->role === 'Admin')
                                <td>
                                    <button class="edit-btn" onclick="editOrder({{ json_encode($order) }})"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteOrder({{ $order->id }})"><i class="fas fa-trash"></i></button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-order-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function createOrder() {
        // Prepare products options
        let productOptions = `
            <option value="" disabled selected>Select Product</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-image="{{ asset('product_images/' . $product->image) }}">
                    {{ $product->name }} - ${{ number_format($product->price, 2) }}
                </option>
            @endforeach
        `;

        Swal.fire({
            title: '{{ __('messages.add_order') }}',
            width: '800px',
            html: `
                <form id="create-order-form" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <div style="margin-bottom: 15px; text-align: left;">
                                <label>{{ __('messages.customer') }}</label>
                                <select name="user_id" id="user_select" class="swal2-input" required style="margin: 5px 0; width: 100%;" onchange="updateLocation()">
                                    <option value="" disabled selected>Select Customer</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-location="{{ $user->location ?? 'N/A' }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom: 15px; text-align: left;">
                                <label>Customer Location</label>
                                <input type="text" id="user_location" class="swal2-input" readonly placeholder="Select a customer" style="margin: 5px 0; width: 100%; background: #f0f0f0; color: #666;">
                            </div>
                            
                            <div style="margin-bottom: 15px; text-align: left;">
                                <label>{{ __('messages.payment_method') }}</label>
                                <select name="payment_method" class="swal2-input" required style="margin: 5px 0; width: 100%;">
                                    <option value="Cash">Cash</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="PayPal">PayPal</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 15px; text-align: left;">
                                <label>{{ __('messages.status') }}</label>
                                <select name="status" class="swal2-input" required style="margin: 5px 0; width: 100%;">
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                             <div style="margin-bottom: 15px; text-align: left;">
                                <label>{{ __('messages.total_amount') }}</label>
                                <input type="text" id="total_amount_display" class="swal2-input" readonly value="$0.00" style="margin: 5px 0; width: 100%; background: #f0f0f0;">
                            </div>
                        </div>

                        <div style="flex: 1; border-left: 1px solid #eee; padding-left: 20px;">
                            <h4 style="text-align: left; margin-top: 0;">Products</h4>
                            <div id="product-list" style="max-height: 300px; overflow-y: auto; margin-bottom: 10px;">
                                <!-- Dynamic rows will appear here -->
                            </div>
                            <button type="button" class="btn-add-product" onclick="addProductRow()">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                </form>
            `,
            didOpen: () => {
                window.productOptions = productOptions; // Make available globally
                addProductRow(); // Add first row
            },
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.add') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
            preConfirm: () => {
                const form = document.getElementById('create-order-form');
                // Validate that at least one product is selected
                const productSelects = form.querySelectorAll('select[name^="products"]');
                let valid = true;
                productSelects.forEach(select => {
                    if (!select.value) valid = false;
                });
                
                if (!valid) {
                    Swal.showValidationMessage('Please select a product for all rows');
                    return false;
                }
                
                form.submit();
            }
        });
    }

    window.addProductRow = function() {
        const container = document.getElementById('product-list');
        const index = container.children.length;
        
        const row = document.createElement('div');
        row.className = 'product-row';
        row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;';
        
        row.innerHTML = `
            <div style="flex: 2;">
                <label style="font-size: 0.8rem;">Product</label>
                <select name="products[${index}][id]" class="swal2-input" style="width: 100%; margin: 0; padding: 5px; height: 35px;" onchange="updateTotal()" required>
                    ${window.productOptions}
                </select>
                <div class="product-preview" style="height: 30px; margin-top: 5px;"></div>
            </div>
            <div style="flex: 1;">
                <label style="font-size: 0.8rem;">Qty</label>
                <input type="number" name="products[${index}][quantity]" value="1" min="1" class="swal2-input" style="width: 100%; margin: 0; padding: 5px; height: 35px;" onchange="updateTotal()" required>
            </div>
            <button type="button" style="border: none; background: none; color: red; cursor: pointer; margin-bottom: 8px;" onclick="this.parentElement.remove(); updateTotal()">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        container.appendChild(row);
    };

    window.updateTotal = function() {
        let total = 0;
        const rows = document.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('input');
            const preview = row.querySelector('.product-preview');
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const price = parseFloat(option.dataset.price);
                const quantity = parseInt(qtyInput.value) || 0;
                const image = option.dataset.image;
                
                total += price * quantity;
                
                preview.innerHTML = `<img src="${image}" style="height: 30px; border-radius: 4px; cursor: pointer;" onclick="showImage('${image}')">`;
            } else {
                preview.innerHTML = '';
            }
        });
        
        document.getElementById('total_amount_display').value = '$' + total.toFixed(2);
    };

    window.updateLocation = function() {
        const select = document.getElementById('user_select');
        const locationInput = document.getElementById('user_location');
        if (select.selectedIndex > 0) {
            const option = select.options[select.selectedIndex];
            locationInput.value = option.dataset.location;
        } else {
            locationInput.value = '';
        }
    };

    function editOrder(order) {
        // Prepare Pre-filled Products
        let itemsHtml = '';
        order.items.forEach((item, index) => {
            let productOptionsForRow = `
                <option value="" disabled>Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-image="{{ asset('product_images/' . $product->image) }}" ${item.product_id == {{ $product->id }} ? 'selected' : ''}>
                        {{ $product->name }} - ${{ number_format($product->price, 2) }}
                    </option>
                @endforeach
            `;
            
            // Build Row directly
            itemsHtml += `
                <div class="product-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">
                    <div style="flex: 2;">
                        <label style="font-size: 0.8rem;">Product</label>
                        <select name="products[${index}][id]" class="swal2-input product-select" style="width: 100%; margin: 0; padding: 5px; height: 35px;" onchange="updateTotalInEdit()" required>
                            ${productOptionsForRow}
                        </select>
                         <div class="product-preview" style="height: 30px; margin-top: 5px;">
                            <img src="${item.product.image ? '/product_images/'+item.product.image : ''}" style="height: 30px; border-radius: 4px; cursor: pointer;" onclick="showImage('/product_images/${item.product.image}')">
                         </div>
                    </div>
                     <div style="flex: 1;">
                        <label style="font-size: 0.8rem;">Qty</label>
                        <input type="number" name="products[${index}][quantity]" value="${item.quantity}" min="1" class="swal2-input qty-input" style="width: 100%; margin: 0; padding: 5px; height: 35px;" onchange="updateTotalInEdit()" required>
                    </div>
                    <button type="button" style="border: none; background: none; color: red; cursor: pointer; margin-bottom: 8px;" onclick="this.parentElement.remove(); updateTotalInEdit()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });

        Swal.fire({
            title: '{{ __('messages.edit_order_status') }}',
            width: '800px',
            html: `
                <form id="edit-order-form" action="/orders/${order.id}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <div style="margin-bottom: 15px; text-align: left;">
                                <label>{{ __('messages.status') }}</label>
                                <select name="status" class="swal2-input" required style="margin: 5px 0; width: 100%;">
                                    <option value="Pending" ${order.status === 'Pending' ? 'selected' : '' }>Pending</option>
                                    <option value="Completed" ${order.status === 'Completed' ? 'selected' : '' }>Completed</option>
                                    <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : '' }>Cancelled</option>
                                </select>
                            </div>
                             <div style="margin-bottom: 15px; text-align: left;">
                                <label>{{ __('messages.total_amount') }}</label>
                                <input type="text" id="edit_total_amount_display" class="swal2-input" readonly value="$${parseFloat(order.total_amount).toFixed(2)}" style="margin: 5px 0; width: 100%; background: #f0f0f0;">
                            </div>
                        </div>

                        <div style="flex: 1; border-left: 1px solid #eee; padding-left: 20px;">
                            <h4 style="text-align: left; margin-top: 0;">Products</h4>
                            <div id="edit-product-list" style="max-height: 300px; overflow-y: auto; margin-bottom: 10px;">
                                ${itemsHtml}
                            </div>
                            <button type="button" class="btn-add-product" onclick="addEditProductRow()">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
            preConfirm: () => {
                const form = document.getElementById('edit-order-form');
                 // Validate that at least one product is selected
                const productSelects = form.querySelectorAll('select[name^="products"]');
                let valid = true;
                if(productSelects.length === 0) valid = false; // Must have at least one product
                productSelects.forEach(select => {
                    if (!select.value) valid = false;
                });
                
                if (!valid) {
                    Swal.showValidationMessage('Please select valid products');
                    return false;
                }
                form.submit();
            }
        });
    }

    window.addEditProductRow = function() {
         const container = document.getElementById('edit-product-list');
        const index = container.children.length + 100; // Offset index to avoid conflicts
        
        const row = document.createElement('div');
        row.className = 'product-row';
        row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;';
        
        row.innerHTML = `
            <div style="flex: 2;">
                <label style="font-size: 0.8rem;">Product</label>
                <select name="products[${index}][id]" class="swal2-input product-select" style="width: 100%; margin: 0; padding: 5px; height: 35px;" onchange="updateTotalInEdit()" required>
                    ${window.productOptions}
                </select>
                <div class="product-preview" style="height: 30px; margin-top: 5px;"></div>
            </div>
            <div style="flex: 1;">
                <label style="font-size: 0.8rem;">Qty</label>
                <input type="number" name="products[${index}][quantity]" value="1" min="1" class="swal2-input qty-input" style="width: 100%; margin: 0; padding: 5px; height: 35px;" onchange="updateTotalInEdit()" required>
            </div>
            <button type="button" style="border: none; background: none; color: red; cursor: pointer; margin-bottom: 8px;" onclick="this.parentElement.remove(); updateTotalInEdit()">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        container.appendChild(row);
        updateTotalInEdit();
    };

    window.updateTotalInEdit = function() {
        let total = 0;
        const container = document.getElementById('edit-product-list');
        if(!container) return;

        const rows = container.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('input');
            const preview = row.querySelector('.product-preview');
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const price = parseFloat(option.dataset.price);
                const quantity = parseInt(qtyInput.value) || 0;
                const image = option.dataset.image;
                
                total += price * quantity;
                
                preview.innerHTML = `<img src="${image}" style="height: 30px; border-radius: 4px; cursor: pointer;" onclick="showImage('${image}')">`;
            } else {
                preview.innerHTML = '';
            }
        });
        
        const totalDisplay = document.getElementById('edit_total_amount_display');
        if(totalDisplay) totalDisplay.value = '$' + total.toFixed(2);
    };

    function deleteOrder(id) {
        Swal.fire({
            title: '{{ __('messages.confirm_delete') }}',
            text: "{{ __('messages.wont_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __('messages.yes_delete') }}',
            cancelButtonText: '{{ __('messages.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('delete-order-form');
                form.action = '/orders/' + id;
                form.submit();
            }
        })
    }
</script>

<style>
    .delete-btn {
        background: none;
        border: none;
        color: #ff416c;
        cursor: pointer;
        font-size: 1.1rem;
        margin-left: 10px;
    }
    .delete-btn:hover {
        color: #d33;
    }
    .edit-btn {
        background: none;
        border: none;
        color: #38bdf8;
        cursor: pointer;
        font-size: 1.1rem;
    }
    .edit-btn:hover {
        color: #0ea5e9;
    }
    .btn-add-product {
        background: #4a90e2;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        width: 100%;
    }
    .btn-add-product:hover {
        background: #357abd;
    }
</style>
@endsection
