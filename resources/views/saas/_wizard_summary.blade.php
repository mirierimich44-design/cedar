{{-- Sticky summary sidebar. Expects: $id (unique DOM id) --}}
<aside>
    <div class="sum-card" id="{{ $id }}">
        <h3><i class="fas fa-shopping-basket"></i> Your Plan</h3>
        <div class="sum-list" data-role="list">
            <div class="sum-empty" data-role="empty">
                <strong>Let's build your plan.</strong>
                Tick any feature on the left and watch your price appear here.
            </div>
        </div>
        <hr class="sum-divider" data-role="divider" style="display:none;">
        <div class="sum-total">
            <span class="sum-total-label">Total</span>
            <div style="text-align:right;">
                <div class="sum-total-amount" data-role="total">KES 0</div>
                <div class="sum-cycle-note" data-role="cycle-note">per month</div>
            </div>
        </div>
        <div class="sum-savings" data-role="savings"></div>
        <div class="sum-trust">
            <div><i class="fas fa-check"></i> No commitment — change anytime.</div>
            <div><i class="fas fa-check"></i> Demo before you pay.</div>
            <div><i class="fas fa-check"></i> Local Kenyan support.</div>
        </div>
    </div>
</aside>
