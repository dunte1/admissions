<div x-data="{
    county: '{{ $formData['personal']['county'] ?? '' }}',
    subCounty: '{{ $formData['personal']['sub_county'] ?? '' }}',
    ward: '{{ $formData['personal']['ward'] ?? '' }}',
    country: '{{ $formData['personal']['country'] ?? 'Kenya' }}',
    physicalChallenge: '{{ $formData['personal']['physical_challenge'] ?? 'no' }}',
    subCounties: [],
    wards: [],
    updateSubCounties() {
        const kenyanCounties = {
            'baringo': ['Baringo Central', 'Baringo East', 'Baringo North', 'Baringo South'],
            'bomet': ['Bomet Central', 'Bomet East', 'Bomet South', 'Konoin', 'Sotik'],
            'bungoma': ['Bungoma Central', 'Bungoma East', 'Bungoma North', 'Bungoma South', 'Kanduyi', 'Kimilili', 'Likuyani'],
            'busia': ['Busia', 'Butula', 'Matayos', 'Nambale', 'Samia'],
            'elgeyo': ['Elgeyo Central', 'Elgeyo North', 'Elgeyo South', 'Keiyo', 'Marakwet'],
            'embu': ['Embu East', 'Embu North', 'Embu West', 'Mbeere North', 'Mbeere South'],
            'homa bay': ['Homa Bay Town', 'Kabondo', 'Kasipul', 'Mbita', 'Ndhiwa', 'Rangwe', 'Rongo', 'Suba'],
            'isiolo': ['Isiolo Central', 'Isiolo North', 'Isiolo South'],
            'kajiado': ['Kajiado Central', 'Kajiado East', 'Kajiado North', 'Kajiado South'],
            'kakamega': ['Kakamega Central', 'Kakamega East', 'Kakamega North', 'Kakamega South', 'Likuyani', 'Lugari', 'Mumias', 'Navakholo'],
            'kericho': ['Kericho Central', 'Kericho East', 'Kericho South', 'Kipkelion', ' Londiani'],
            'kiambu': ['Githunguri', 'Juja', 'Kabas', 'Kari', 'Kikuyu', 'Kiambu', 'Limuru', 'Ruiru', 'Thika'],
            'kilifi': ['Kilifi North', 'Kilifi South', 'Malindi', 'Magarini', 'Mombasa'],
            'kirinyaga': ['Gichuru', 'Kutus', 'Mwea', 'Ndia', 'Keri'],
            'kisii': ['Bonchari', 'Kitutu Chache', 'Kitutu Masaba', 'Nyakibasi', 'South Mugirang', 'Gucha'],
            'kisumu': ['Kisumu Central', 'Kisumu East', 'Kisumu North', 'Kisumu West', 'Seme', 'Nyando', 'Muhoroni'],
            'kitale': ['Kapenguria', 'Kacheliba', 'Sigor', 'Pokot South', 'Tiaty', 'West Pokot'],
            'kwale': ['Kinango', 'Lunga Lunga', 'Matuga', 'Msambweni', 'Umoja'],
            'laikipia': ['Central', 'East', 'North', 'West'],
            'lamu': ['Faza', 'Kiunga', 'Lamu Island', 'Manda'],
            'machakos': ['Kathiani', 'Masinga', 'Matungulu', 'Mwala', 'Yatta'],
            'makueni': ['Kaiti', 'Kibwezi', 'Kilungu', 'Makindu', 'Mbooni', 'Nthuki'],
            'mandera': ['Banissa', 'Goliath', 'Lafey', 'Mandera East', 'Mandera North', 'Mandera West'],
            'marsabit': ['Chalbi', 'Laisamis', 'Loima', 'Marsabit Central', 'North Horr', 'Saku'],
            'meru': ['Buuri', 'Central', 'Igembe South', 'Imenti North', 'Imenti South', 'Tigania', 'West'],
            'migori': ['Awendo', 'Kuria', 'Macalder', 'Migori Town', 'Nyatike', 'Rongo', 'Suna'],
            'mombasa': ['Changamwe', 'Jomvu', 'Kisauni', 'Likoni', 'Mtongwe', 'Nyali', 'Shanzu'],
            'muranga': ['Gatanga', 'Kandara', 'Kangema', 'Kiharu', 'Mathioya', 'Mukure'],
            'nairobi': ['Dagoretti', 'Embakasi', 'Kasarani', 'Langata', 'Makadara', 'Mathare', 'Roysy', 'Starehe', 'Westlands'],
            'nakuru': ['Bahati', 'Gil Gil', 'Kuresoi', 'Molo', 'Naivasha', 'Njoro', 'Rongai', 'Subukoni'],
            'nandi': ['Chesumei', 'Emgwen', 'Kabet', 'Kosirai', 'Mosop', 'Tinder'],
            'narok': ['Narok East', 'Narok North', 'Narok South', 'Narok West', 'Kilgoris'],
            'nyamira': ['Borabu', 'Kitutu Masaba', 'North Mugirang', 'Wanjare'],
            'nyandarua': ['Kinangop', 'Kipipl', 'Mukoe', 'Oljoro', 'Shabaha'],
            'nyeri': ['Kieni', 'Mathira', 'Mukurweini', 'Nyeri Central', 'Othaya', 'Tetu'],
            'samburu': ['Buffalo', 'Laisamis', 'Leroghi', 'Samburu Central', 'Samburu North'],
            'siaya': ['Alego', 'Bondo', 'Gem', 'Rarieda', 'Siaya Town', 'Ukambani'],
            'taita': ['Mbolu', 'Ngata', 'Pokomo', 'Taveta', 'Wusi'],
            'tana river': ['Bahi', 'Garsen', 'Kipini', 'Malak', 'Watoto'],
            'tharaka': ['Chuka', 'Ign', 'Maara', 'Makandara', 'Tharaka Central'],
            'trans nzoia': ['Cherangany', 'Endebess', 'Kwanza', 'Saboti', 'Temoyto'],
            'turkana': ['Central', 'Daiwa', 'Kakuma', 'Kalob', 'Lapur', 'Loima', 'North'],
            'uasin gishu': ['Ainabkoi', 'Kapso', 'Kesses', 'Moiben', 'Soy', 'Turbo'],
            'vihiga': ['Em Bukanga', 'Hamisi', 'Luanda', 'Sabatia', 'Vihiga'],
            'wajir': ['Aisal', 'Buna', 'E/Wajir', 'Had', 'Tarbaj', 'Wajir North', 'Wajir West'],
            'west pokot': ['Bet', 'Kochop', 'Lelan', 'Mtiol', 'Patt', 'Pokot South'],
        };
        this.subCounties = kenyanCounties[this.county.toLowerCase()] || [];
    },
    updateWards() {
        this.wards = [];
    }
}" class="space-y-4">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
            <input type="text" name="first_name" value="{{ $formData['personal']['first_name'] ?? auth()->user()->first_name ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Second Name</label>
            <input type="text" name="middle_name" value="{{ $formData['personal']['middle_name'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
            <input type="text" name="last_name" value="{{ $formData['personal']['last_name'] ?? auth()->user()->last_name ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
            <select name="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Gender</option>
                <option value="male" {{ ($formData['personal']['gender'] ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ ($formData['personal']['gender'] ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
            <input type="date" name="date_of_birth" value="{{ $formData['personal']['date_of_birth'] ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">National ID / Passport No.</label>
            <input type="text" name="id_number" value="{{ $formData['personal']['id_number'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="National ID or Passport Number">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
            <select name="religion" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Religion</option>
                <option value="catholic" {{ ($formData['personal']['religion'] ?? '') == 'catholic' ? 'selected' : '' }}>Catholic</option>
                <option value="protestant" {{ ($formData['personal']['religion'] ?? '') == 'protestant' ? 'selected' : '' }}>Protestant</option>
                <option value="muslim" {{ ($formData['personal']['religion'] ?? '') == 'muslim' ? 'selected' : '' }}>Muslim</option>
                <option value="other" {{ ($formData['personal']['religion'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
    </div>

    <hr class="my-4 border-gray-200">

    <h4 class="font-semibold text-gray-900 mb-3">Contact Information</h4>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
            <input type="tel" name="phone" value="{{ $formData['personal']['phone'] ?? auth()->user()->phone ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="+254700000000">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone Number</label>
            <input type="tel" name="alt_phone" value="{{ $formData['personal']['alt_phone'] ?? '' }}" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="+254700000000">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
        <input type="email" name="email" value="{{ $formData['personal']['email'] ?? auth()->user()->email ?? '' }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
            placeholder="email@example.com">
    </div>

    <hr class="my-4 border-gray-200">

    <h4 class="font-semibold text-gray-900 mb-3">Location Information</h4>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
            <input type="text" name="country" value="{{ $formData['personal']['country'] ?? 'Kenya' }}" readonly
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">County *</label>
            <select name="county" x-model="county" @change="updateSubCounties()" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select County</option>
                @php
                    $counties = ['Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet', 'Embu', 'Garissa', 'Homa Bay', 'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii', 'Kisumu', 'Kitale', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera', 'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Muranga', 'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita-Taveta', 'Tana River', 'Tharaka-Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'];
                @endphp
                @foreach($counties as $c)
                    <option value="{{ strtolower($c) }}" {{ ($formData['personal']['county'] ?? '') == strtolower($c) ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sub-county *</label>
            <select name="sub_county" x-model="subCounty" @change="updateWards()" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Sub-county</option>
                <template x-for="sc in subCounties">
                    <option :value="sc" x-text="sc"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ward</label>
            <input type="text" name="ward" value="{{ $formData['personal']['ward'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="Ward">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Town / Village</label>
            <input type="text" name="town" value="{{ $formData['personal']['town'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="Town or Village">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Address</label>
            <input type="text" name="postal_address" value="{{ $formData['personal']['postal_address'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="P.O. Box 1234">
        </div>
    </div>

    <hr class="my-4 border-gray-200">

    <h4 class="font-semibold text-gray-900 mb-3">Additional Information</h4>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Do you have any physical challenges?</label>
        <select name="physical_challenge" x-model="physicalChallenge" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <option value="no" {{ ($formData['personal']['physical_challenge'] ?? 'no') == 'no' ? 'selected' : '' }}>No</option>
            <option value="yes" {{ ($formData['personal']['physical_challenge'] ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    <div x-show="physicalChallenge === 'yes'" x-transition>
        <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, describe *</label>
        <textarea name="physical_challenge_description" rows="3" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
            placeholder="Please describe your physical challenge...">{{ $formData['personal']['physical_challenge_description'] ?? '' }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nationality *</label>
        <input type="text" name="nationality" value="{{ $formData['personal']['nationality'] ?? 'Kenyan' }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
    </div>
</div>