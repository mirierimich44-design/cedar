{{-- Redesigned Totals - Unified Card --}}
<div class="pos-right-totals">
    <div class="unified-totals-card">
        <div class="totals-main">
            <span class="totals-label">Total Payable</span>
            <span class="totals-value total_payable_span" id="total_payable">0.00</span>
        </div>
        
        <div class="totals-details">
            <div class="detail-row">
                <span>Subtotal</span>
                <span class="price_total" id="subtotal_display">0.00</span>
            </div>
            <div class="detail-row clickable" data-toggle="modal" data-target="#posEditDiscountModal">
                <span>Discount <i class="fas fa-edit"></i></span>
                <span class="discount-value">- <span id="total_discount">0.00</span></span>
            </div>
            <div class="detail-row clickable" data-toggle="modal" data-target="#posEditOrderTaxModal">
                <span>Tax <i class="fas fa-edit"></i></span>
                <span class="tax-value"><span id="order_tax">0.00</span></span>
            </div>
            {{-- Round Off (Hidden by default, used for logic) --}}
            <div class="detail-row round_off_row hide">
                <span>Round Off</span>
                <span id="round_off_text">0.00</span>
            </div>
        </div>
    </div>
</div>

{{-- Products Panel (Conditionally Visible) --}}
@if (empty($pos_settings['hide_product_suggestion']) && empty($pos_settings['hide_products_panel']))
<div class="products-panel">
    {{-- Products Header --}}
    <div class="products-header">
        <div class="header-left">
            <i class="fas fa-box-open"></i>
            <h2 class="products-title">Products</h2>
        </div>
        <button type="button" class="btn-modal quick-add-btn"
            data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}"
            data-container=".quick_add_product_modal" title="Quick Add Product">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    {{-- Product Search in Sidebar --}}
    <div class="products-search">
        <div class="search-icon">
            <i class="fas fa-search"></i>
        </div>
        <input type="text" id="product_search_sidebar" class="form-control search-input" placeholder="Search products...">
    </div>

    {{-- Category Filter (optional) --}}
    <div class="category-filter" id="category_filter">
        {{-- Categories will be loaded here --}}
    </div>

    {{-- Favorites / Quick Access Bar --}}
    <div id="favorites_bar" class="favorites-bar hide">
        <div class="favorites-header">
            <i class="fas fa-star"></i>
            <span>Favorites</span>
        </div>
        <div id="favorites_list" class="favorites-list">
            {{-- Favorites will be loaded here --}}
        </div>
    </div>

    {{-- Product Grid Area --}}
    <div class="products-grid-container">
        <div id="product_list_body" class="products-grid">
            {{-- Product cards will be loaded here via AJAX --}}
        </div>

        {{-- Loader --}}
        <div id="suggestion_page_loader" class="products-loader hide">
            <div class="loader-spinner">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
            </div>
            <span>Loading products...</span>
        </div>

        {{-- Empty State --}}
        <div id="no_products_found" class="empty-state hide">
            <i class="fas fa-box-open"></i>
            <p>No products found</p>
        </div>
    </div>

    {{-- Featured Products Toggle --}}
    @if (!empty($featured_products))
        <div class="featured-toggle">
            <button type="button" id="show_featured_products" class="featured-btn">
                <i class="fas fa-star"></i>
                @lang('lang_v1.featured_products')
            </button>
        </div>
    @endif
</div>

<input type="hidden" id="suggestion_page" value="1">
@endif
