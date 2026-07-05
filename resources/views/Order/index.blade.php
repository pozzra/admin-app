@extends('layouts.admin')

@section('title', 'Order Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-receipt" style="margin-right: 10px; color: var(--primary);"></i> {{ __('messages.orders') }}</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <form method="GET" action="{{ route('orders.index') }}" style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                    <select name="per_page" class="per-page-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <button class="btn-primary" onclick="createOrder()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_order') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <td>Order ID</td>
                            <td>{{ __('messages.customer') }}</td>
                            <td>{{ __('messages.total_amount') }}</td>
                            <td>{{ __('messages.payment_method') }}</td>
                            <td>Status</td>
                            <td>{{ __('messages.date') }}</td>
                            @if(Auth::user()->role === 'Admin')
                            <td>{{ __('messages.action') }}</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td style="font-weight: 600;">#{{ $order->id }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($order->user->image)
                                        <img src="{{ asset('user_images/' . $order->user->image) }}" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-main); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                                        </div>
                                    @endif
                                    {{ $order->user->name }}
                                </div>
                            </td>
                            <td style="font-weight: 600; color: var(--primary);">${{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>
                                <span class="badge {{ strtolower($order->status) == 'completed' ? 'badge-success' : (strtolower($order->status) == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            @if(Auth::user()->role === 'Admin')
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="edit-btn" onclick="editOrder({{ json_encode($order) }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteOrder({{ $order->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

<form id="delete-order-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    const allProducts = @json($products);
    let productIndex = 0;

    function getProductRowHtml(index, selectedId = '', quantity = 1) {
        let options = allProducts.map(p => 
            `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''} data-price="${p.price}">${p.name} ($${parseFloat(p.price).toFixed(2)})</option>`
        ).join('');

        return `
            <div class="product-item" id="product-row-${index}" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 10px; margin-bottom: 10px; align-items: center;">
                <select name="products[${index}][id]" class="swal2-input product-select" style="width: 100%; margin: 0;" required onchange="updateTotal()">
                    <option value="" data-price="0">Select Product</option>
                    ${options}
                </select>
                <input type="number" name="products[${index}][quantity]" class="swal2-input quantity-input" value="${quantity}" min="1" placeholder="Qty" style="width: 100%; margin: 0;" required oninput="updateTotal()">
                <button type="button" onclick="removeProductRow(${index})" style="background: var(--danger-soft); color: var(--danger); border: none; border-radius: 4px; padding: 8px 12px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }

    function addProductRow(containerId) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.innerHTML = getProductRowHtml(productIndex++);
        container.appendChild(div.firstElementChild);
        updateTotal();
    }

    function removeProductRow(index) {
        const row = document.getElementById(`product-row-${index}`);
        if (row) {
            const container = row.parentElement;
            if (container.children.length > 1) {
                row.remove();
                updateTotal();
            } else {
                Swal.showValidationMessage('At least one product is required');
            }
        }
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.product-item').forEach(row => {
            const select = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.quantity-input');
            const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price') || 0);
            const qty = parseInt(qtyInput.value || 0);
            total += price * qty;
        });

        const totalDisplays = document.querySelectorAll('.total-amount-value');
        totalDisplays.forEach(el => el.textContent = total.toFixed(2));
        
        const khqrAmount = document.querySelector('.khqr-amount');
        if (khqrAmount) khqrAmount.textContent = '$' + total.toFixed(2);
    }

    function toggleKHQR(method) {
        const container = document.getElementById('khqr-section');
        if (container) {
            if (method === 'ABA' || method === 'WING') {
                container.style.display = 'block';
                updateTotal();
            } else {
                container.style.display = 'none';
            }
        }
    }

    function createOrder() {
        productIndex = 0;
        Swal.fire({
            title: '{{ __('messages.add_order') }}',
            html: `
                <form id="create-order-form" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div style="text-align: left; margin-bottom: 1.25rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Customer</label>
                        <select name="user_id" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="text-align: left; margin-bottom: 1.25rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Payment Method</label>
                        <select name="payment_method" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;" onchange="toggleKHQR(this.value)">
                            <option value="Cash">Cash</option>
                            <option value="ABA">ABA Pay</option>
                            <option value="WING">WING</option>
                        </select>
                    </div>

                    <div id="khqr-section" style="display: none; margin-bottom: 1.25rem; padding: 1rem; background: #f8fafc; border-radius: 8px; border: 2px dashed #e2e8f0; text-align: center;">
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Scan to Pay (KHQR)</p>
                        <div style="width: 120px; height: 120px; background: white; margin: 0 auto 0.5rem; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                            <i class="fas fa-qrcode" style="font-size: 4rem; color: #1e293b;"></i>
                        </div>
                        <p class="khqr-amount" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">$0.00</p>
                    </div>

                    <div style="text-align: left; margin-bottom: 1.25rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Status</label>
                        <select name="status" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div style="text-align: left; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label style="font-size: 0.875rem; font-weight: 600;">Select Products</label>
                            <button type="button" onclick="addProductRow('product-selection-container')" style="background: var(--primary-soft); color: var(--primary); border: none; border-radius: 4px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer;">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                        <div id="product-selection-container">
                        </div>
                        <div style="margin-top: 1rem; text-align: right; font-weight: 700; border-top: 1px solid var(--border-color); padding-top: 0.5rem;">
                            Total: <span class="total-amount-value" style="color: var(--primary);">0.00</span>
                        </div>
                    </div>
                </form>
            `,
            didOpen: () => {
                addProductRow('product-selection-container');
            },
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            width: '600px',
            preConfirm: () => {
                const form = document.getElementById('create-order-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function editOrder(order) {
        productIndex = 0;
        let usersHtml = `@foreach($users as $user)
            <option value="{{ $user->id }}" ${order.user_id == {{ $user->id }} ? 'selected' : ''}>{{ $user->name }} ({{ $user->email }})</option>
        @endforeach`;

        Swal.fire({
            title: '{{ __('messages.edit_order') }}',
            html: `
                <form id="edit-order-form" action="/orders/${order.id}" method="POST">
                    @csrf
                    @method('PUT')
                    <div style="text-align: left; margin-bottom: 1.25rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Customer</label>
                        <select name="user_id" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            ${usersHtml}
                        </select>
                    </div>
                    <div style="text-align: left; margin-bottom: 1.25rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Payment Method</label>
                        <select name="payment_method" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;" onchange="toggleKHQR(this.value)">
                            <option value="Cash" ${order.payment_method === 'Cash' ? 'selected' : ''}>Cash</option>
                            <option value="ABA" ${order.payment_method === 'ABA' ? 'selected' : ''}>ABA Pay</option>
                            <option value="WING" ${order.payment_method === 'WING' ? 'selected' : ''}>WING</option>
                        </select>
                    </div>

                    <div id="khqr-section" style="display: ${order.payment_method === 'ABA' || order.payment_method === 'WING' ? 'block' : 'none'}; margin-bottom: 1.25rem; padding: 1rem; background: #f8fafc; border-radius: 8px; border: 2px dashed #e2e8f0; text-align: center;">
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Scan to Pay (KHQR)</p>
                        <div style="width: 120px; height: 120px; background: white; margin: 0 auto 0.5rem; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                            <i class="fas fa-qrcode" style="font-size: 4rem; color: #1e293b;"></i>
                        </div>
                        <p class="khqr-amount" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">$${parseFloat(order.total_amount).toFixed(2)}</p>
                    </div>

                    <div style="text-align: left; margin-bottom: 1.25rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Status</label>
                        <select name="status" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Completed" ${order.status === 'Completed' ? 'selected' : ''}>Completed</option>
                            <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </div>
                    <div style="text-align: left; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label style="font-size: 0.875rem; font-weight: 600;">Products</label>
                            <button type="button" onclick="addProductRow('edit-product-selection-container')" style="background: var(--primary-soft); color: var(--primary); border: none; border-radius: 4px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer;">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                        <div id="edit-product-selection-container">
                        </div>
                        <div style="margin-top: 1rem; text-align: right; font-weight: 700; border-top: 1px solid var(--border-color); padding-top: 0.5rem;">
                            Total: <span class="total-amount-value" style="color: var(--primary);">${parseFloat(order.total_amount).toFixed(2)}</span>
                        </div>
                    </div>
                </form>
            `,
            didOpen: () => {
                const container = document.getElementById('edit-product-selection-container');
                if (order.items && order.items.length > 0) {
                    order.items.forEach(item => {
                        const div = document.createElement('div');
                        div.innerHTML = getProductRowHtml(productIndex++, item.product_id, item.quantity);
                        container.appendChild(div.firstElementChild);
                    });
                } else {
                    addProductRow('edit-product-selection-container');
                }
                updateTotal();
            },
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            width: '600px',
            preConfirm: () => {
                const form = document.getElementById('edit-order-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function deleteOrder(id) {
        Swal.fire({
            title: '{{ __('messages.confirm_delete') }}',
            text: "{{ __('messages.wont_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
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
        background: var(--danger-soft);
        border: none;
        color: var(--danger);
        cursor: pointer;
        font-size: 0.875rem;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .delete-btn:hover {
        background: var(--danger);
        color: #fff;
    }
    .edit-btn {
        background: var(--primary-soft);
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 0.875rem;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .edit-btn:hover {
        background: var(--primary);
        color: #fff;
    }
    
    .search-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        outline: none;
        background: var(--bg-main);
        color: var(--text-main);
    }
    
    .per-page-select {
        padding: 0.5rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        outline: none;
        background: var(--bg-main);
        color: var(--text-main);
        cursor: pointer;
    }
</style>
@endsection
