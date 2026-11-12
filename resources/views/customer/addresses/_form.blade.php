
@csrf

<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Full Name *</label>
        <input name="name" class="form-control" required
               value="{{ old('name', $address->name ?? auth()->user()->name) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone *</label>
        <input name="phone" class="form-control" required
               value="{{ old('phone', $address->phone ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Alternate Phone</label>
        <input name="alternate_phone" class="form-control"
               value="{{ old('alternate_phone', $address->alternate_phone ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Address Type *</label>
        <select name="address_type" class="form-select" required>
            @foreach(['home' => 'Home', 'work' => 'Work', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('address_type', $address->address_type ?? 'home') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Address Line 1 *</label>
        <input name="address_line1" class="form-control" required
               value="{{ old('address_line1', $address->address_line1 ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Address Line 2</label>
        <input name="address_line2" class="form-control"
               value="{{ old('address_line2', $address->address_line2 ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Landmark</label>
        <input name="landmark" class="form-control"
               value="{{ old('landmark', $address->landmark ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">City *</label>
        <input name="city" class="form-control" required
               value="{{ old('city', $address->city ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">State *</label>
        <input name="state" class="form-control" required
               value="{{ old('state', $address->state ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">PIN Code *</label>
        <input name="pincode" class="form-control" maxlength="6"
               inputmode="numeric" pattern="[0-9]{6}" required
               value="{{ old('pincode', $address->pincode ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Country *</label>
        <input name="country" class="form-control" required
               value="{{ old('country', $address->country ?? 'India') }}">
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_default" value="1"
                   class="form-check-input" id="is_default"
                   @checked(old('is_default', $address->is_default ?? false))>

            <label class="form-check-label" for="is_default">
                Set as default delivery address
            </label>
        </div>
    </div>

</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4">
        <i class="fas fa-save me-2"></i>Save Address
    </button>

    <a href="{{ route('account.addresses.index') }}"
       class="btn btn-light px-4">Cancel</a>
</div>
