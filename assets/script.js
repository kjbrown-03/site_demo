const properties = [
    {
        id: 1,
        price: 485000,
        beds: 4,
        baths: 2,
        sqft: 1883,
        address: "123 Avenue des Champs, Paris 8ème",
        image: "https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800",
        status: "Nouveau",
        agent: "Sophie Martin"
    },
    {
        id: 2,
        price: 325000,
        beds: 3,
        baths: 2,
        sqft: 1440,
        address: "45 Rue de la République, Lyon 2ème",
        image: "https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800",
        status: "Prix réduit",
        agent: "Jean Dupont"
    },
    {
        id: 3,
        price: 629000,
        beds: 5,
        baths: 3,
        sqft: 2819,
        address: "78 Boulevard Haussmann, Paris 9ème",
        image: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800",
        status: "Nouveau",
        agent: "Marie Leclerc"
    },
    {
        id: 4,
        price: 389900,
        beds: 3,
        baths: 2,
        sqft: 2000,
        address: "12 Rue Victor Hugo, Bordeaux",
        image: "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800",
        status: "À vendre",
        agent: "Pierre Dubois"
    },
    {
        id: 5,
        price: 725000,
        beds: 4,
        baths: 3,
        sqft: 2500,
        address: "89 Promenade des Anglais, Nice",
        image: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800",
        status: "Nouveau",
        agent: "Claire Bernard"
    },
    {
        id: 6,
        price: 295000,
        beds: 2,
        baths: 1,
        sqft: 1200,
        address: "34 Rue de la Paix, Lille",
        image: "https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=800",
        status: "Prix réduit",
        agent: "Thomas Rousseau"
    },
    {
        id: 7,
        price: 550000,
        beds: 4,
        baths: 2,
        sqft: 2200,
        address: "156 Avenue de la Liberté, Toulouse",
        image: "https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?w=800",
        status: "À vendre",
        agent: "Isabelle Moreau"
    },
    {
        id: 8,
        price: 445000,
        beds: 3,
        baths: 2,
        sqft: 1750,
        address: "67 Cours Mirabeau, Aix-en-Provence",
        image: "https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800",
        status: "Nouveau",
        agent: "Luc Fontaine"
    }
];

// For the PHP index page, we'll check if propertiesGrid exists before trying to populate it
const propertiesGrid = document.getElementById('propertiesGrid');
const searchInput = document.querySelector('.search-input');
const searchBtn = document.querySelector('.search-btn');
const tabBtns = document.querySelectorAll('.tab-btn');
const prevBtn = document.querySelector('.carousel-btn.prev');
const nextBtn = document.querySelector('.carousel-btn.next');

// Only run carousel code if we're on a page with these elements
if (propertiesGrid) {
    let currentIndex = 0;
    const itemsPerPage = 4;

    function formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 0
        }).format(price);
    }

    function createPropertyCard(property) {
        const card = document.createElement('div');
        card.className = 'property-card';
        card.innerHTML = `
            <div class="property-image" style="background-image: url('${property.image}');">
                <span class="property-badge">${property.status}</span>
                <div class="property-favorite">
                    <i class="far fa-heart"></i>
                </div>
            </div>
            <div class="property-info">
                <div class="property-price">${formatPrice(property.price)}</div>
                <div class="property-details">
                    <div class="property-detail">
                        <i class="fas fa-bed"></i>
                        <span>${property.beds} ch</span>
                    </div>
                    <div class="property-detail">
                        <i class="fas fa-bath"></i>
                        <span>${property.baths} sdb</span>
                    </div>
                    <div class="property-detail">
                        <i class="fas fa-ruler-combined"></i>
                        <span>${property.sqft} m²</span>
                    </div>
                </div>
                <div class="property-address">${property.address}</div>
                <div class="property-meta">Agent: ${property.agent}</div>
            </div>
        `;

        const favoriteBtn = card.querySelector('.property-favorite');
        favoriteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const icon = favoriteBtn.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
            favoriteBtn.style.color = icon.classList.contains('fas') ? '#FF4757' : '';
        });

        card.addEventListener('click', () => {
            window.location.href = 'pages/property_detail.php?id=' + property.id;
        });

        return card;
    }

    function renderProperties(propertiesToRender = properties) {
        propertiesGrid.innerHTML = '';
        propertiesToRender.forEach(property => {
            propertiesGrid.appendChild(createPropertyCard(property));
        });
    }

    function showPropertyDetails(property) {
        window.location.href = 'pages/property_detail.php?id=' + property.id;
    }

    function handleSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredProperties = properties.filter(property => 
            property.address.toLowerCase().includes(searchTerm)
        );
        renderProperties(filteredProperties);
    }

    function handleTabSwitch(e) {
        tabBtns.forEach(btn => btn.classList.remove('active'));
        e.target.classList.add('active');
        
        const tab = e.target.dataset.tab;
        searchInput.placeholder = tab === 'buy' ? 'Ville, quartier, code postal...' :
                               tab === 'rent' ? 'Rechercher une location...' :
                               'Adresse ou code postal...';
    }

    function slideCarousel(direction) {
        const maxIndex = Math.max(0, properties.length - itemsPerPage);
        
        if (direction === 'next') {
            currentIndex = Math.min(currentIndex + 1, maxIndex);
        } else {
            currentIndex = Math.max(currentIndex - 1, 0);
        }
        
        const offset = -currentIndex * (320 + 30);
        propertiesGrid.style.transform = `translateX(${offset}px)`;
        propertiesGrid.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
    }

    // Event listeners
    if (searchBtn) {
        searchBtn.addEventListener('click', handleSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                handleSearch();
            }
        });
    }

    if (tabBtns) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', handleTabSwitch);
        });
    }

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => slideCarousel('prev'));
        nextBtn.addEventListener('click', () => slideCarousel('next'));
    }

    // Initial render
    document.addEventListener('DOMContentLoaded', () => {
        renderProperties();
        
        const serviceCards = document.querySelectorAll('.service-card');
        serviceCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            // Note: Observer code removed for simplicity
        });

        const statItems = document.querySelectorAll('.stat-item');
        statItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = `all 0.5s ease ${index * 0.1}s`;
            // Note: Observer code removed for simplicity
        });

        const filterSelects = document.querySelectorAll('.filter-item select');
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                filterProperties();
            });
        });
    });
}

function filterProperties() {
    const typeFilter = document.querySelectorAll('.filter-item select')[0].value;
    const priceFilter = document.querySelectorAll('.filter-item select')[1].value;
    const bedsFilter = document.querySelectorAll('.filter-item select')[2].value;
    const bathsFilter = document.querySelectorAll('.filter-item select')[3].value;

    let filtered = [...properties];

    if (priceFilter !== 'Tous') {
        const maxPrice = parseInt(priceFilter.replace(/\D/g, ''));
        if (!isNaN(maxPrice)) {
            filtered = filtered.filter(p => p.price <= maxPrice);
        }
    }

    if (bedsFilter !== 'Toutes') {
        const minBeds = parseInt(bedsFilter);
        if (!isNaN(minBeds)) {
            filtered = filtered.filter(p => p.beds >= minBeds);
        }
    }

    if (bathsFilter !== 'Toutes') {
        const minBaths = parseInt(bathsFilter);
        if (!isNaN(minBaths)) {
            filtered = filtered.filter(p => p.baths >= minBaths);
        }
    }

    if (propertiesGrid) {
        renderProperties(filtered);
    }
}

// Navbar scroll effect
const navbar = document.querySelector('.navbar');
let lastScroll = 0;

if (navbar) {
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > lastScroll && currentScroll > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScroll = currentScroll;
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});