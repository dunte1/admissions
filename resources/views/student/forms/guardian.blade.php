<div x-data="{
    relationship: '{{ $formData['guardian']['relationship'] ?? '' }}',
    showOtherRelationship: false,
    hasAltContact: {{ ($formData['guardian']['has_alt_contact'] ?? false) ? 'true' : 'false' }},
    county: '{{ $formData['guardian']['county'] ?? '' }}',
    subCounties: [],
    init() {
        this.updateVisibility();
    },
    updateVisibility() {
        this.showOtherRelationship = this.relationship === 'other';
        this.hasAltContact = document.querySelector('[name=\'has_alt_contact\']')?.checked || {{ ($formData['guardian']['has_alt_contact'] ?? false) ? 'true' : 'false' }};
    },
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
            'kericho': ['Kericho Central', 'Kericho East', 'Kericho South', 'Kipkelion', 'Londiani'],
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
    }
}" class="space-y-4">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Full Name *</label>
        <input type="text" name="guardian_name" value="{{ $formData['guardian']['guardian_name'] ?? '' }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Guardian National ID Number *</label>
            <input type="text" name="guardian_id_number" value="{{ $formData['guardian']['guardian_id_number'] ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="National ID Number">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Relationship *</label>
            <select name="relationship" x-model="relationship" @change="updateVisibility()" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select</option>
                <option value="parent" {{ ($formData['guardian']['relationship'] ?? '') == 'parent' ? 'selected' : '' }}>Parent</option>
                <option value="guardian" {{ ($formData['guardian']['relationship'] ?? '') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                <option value="sibling" {{ ($formData['guardian']['relationship'] ?? '') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                <option value="relative" {{ ($formData['guardian']['relationship'] ?? '') == 'relative' ? 'selected' : '' }}>Relative</option>
                <option value="employer" {{ ($formData['guardian']['relationship'] ?? '') == 'employer' ? 'selected' : '' }}>Employer</option>
                <option value="other" {{ ($formData['guardian']['relationship'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
    </div>

    <div x-show="showOtherRelationship" x-transition>
        <label class="block text-sm font-medium text-gray-700 mb-1">Specify Relationship *</label>
        <input type="text" name="other_relationship" value="{{ $formData['guardian']['other_relationship'] ?? '' }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
            placeholder="Please specify your relationship to this person">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
            <input type="tel" name="guardian_phone" value="{{ $formData['guardian']['guardian_phone'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="+254700000000">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="guardian_email" value="{{ $formData['guardian']['guardian_email'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                placeholder="email@example.com">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">County</label>
            <select name="county" x-model="county" @change="updateSubCounties()" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select County</option>
                @php
                    $counties = ['Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet', 'Embu', 'Garissa', 'Homa Bay', 'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii', 'Kisumu', 'Kitale', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera', 'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Muranga', 'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita-Taveta', 'Tana River', 'Tharaka-Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'];
                @endphp
                @foreach($counties as $c)
                    <option value="{{ strtolower($c) }}" {{ ($formData['guardian']['county'] ?? '') == strtolower($c) ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sub-county</label>
            <select name="sub_county" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Sub-county</option>
                <template x-for="sc in subCounties">
                    <option :value="sc" x-text="sc"></option>
                </template>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
        <input type="text" name="guardian_occupation" value="{{ $formData['guardian']['guardian_occupation'] ?? '' }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <textarea name="guardian_address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ $formData['guardian']['guardian_address'] ?? '' }}</textarea>
    </div>

    <hr class="my-4 border-gray-200">

    <div>
        <label class="flex items-center">
            <input type="checkbox" name="has_alt_contact" value="1" 
                {{ ($formData['guardian']['has_alt_contact'] ?? '') ? 'checked' : '' }}
                @change="updateVisibility()"
                class="w-4 h-4 text-purple-600 border-gray-300 rounded">
            <span class="ml-2 text-sm text-gray-700">Has Alternative Contact</span>
        </label>
    </div>

    <div x-show="hasAltContact" x-transition class="space-y-4 pt-2">
        <h4 class="font-semibold text-gray-700">Alternative Contact Details</h4>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input type="text" name="alt_name" value="{{ $formData['guardian']['alt_name'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship *</label>
                <select name="alt_relationship" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Select</option>
                    <option value="parent">Parent</option>
                    <option value="guardian">Guardian</option>
                    <option value="sibling">Sibling</option>
                    <option value="relative">Relative</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                <input type="tel" name="alt_phone" value="{{ $formData['guardian']['alt_phone'] ?? '' }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                    placeholder="+254700000000">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="alt_email" value="{{ $formData['guardian']['alt_email'] ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
    </div>
</div>