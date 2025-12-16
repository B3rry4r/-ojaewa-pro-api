<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        
        $blogs = [
            [
                'title' => 'Celebrating African Fashion: A Guide to Traditional and Modern Styles',
                'body' => 'African fashion is a vibrant tapestry that weaves together centuries of tradition with contemporary innovation. From the colorful kente cloth of Ghana to the flowing agbada of Nigeria, traditional African garments tell stories of heritage, status, and cultural identity.

In recent years, African fashion has gained global recognition, with designers like Ozwald Boateng, Duro Olowu, and Lisa Folawiyo making waves on international runways. This renaissance has brought traditional African textiles and designs to the forefront of global fashion consciousness.

The beauty of African fashion lies in its diversity. Each region has its own unique textile traditions:

**West Africa**: Known for vibrant prints like Adire, Aso Oke, and Wax prints. The intricate indigo dyeing techniques create stunning patterns that have been passed down through generations.

**East Africa**: Features elegant flowing fabrics and geometric patterns. Ethiopian cotton weaving and Maasai beadwork showcase the region\'s artistic heritage.

**Southern Africa**: Renowned for its sophisticated tailoring and unique prints, including the distinctive shweshwe fabric from South Africa.

Modern African designers are reimagining these traditional elements, creating contemporary pieces that honor the past while embracing the future. They\'re proving that African fashion is not just about preserving tradition—it\'s about evolution and innovation.

The global fashion industry has taken notice, with major brands collaborating with African designers and incorporating African-inspired elements into their collections. This has opened new markets and opportunities for African fashion entrepreneurs.

For fashion enthusiasts looking to incorporate African elements into their wardrobe, start with accessories like scarves, jewelry, or bags. These pieces can add a pop of color and cultural significance to any outfit. As you become more comfortable, consider investing in statement pieces like printed blazers or flowing maxi dresses.

The future of African fashion looks bright, with young designers using technology and social media to reach global audiences. E-commerce platforms are making it easier than ever to purchase authentic African fashion pieces directly from the continent.

African fashion is more than just clothing—it\'s a celebration of culture, craftsmanship, and creativity. By supporting African designers and brands, we\'re not just buying clothes; we\'re investing in communities and preserving cultural heritage for future generations.',
                'featured_image' => 'https://via.placeholder.com/800x600.png?text=African+Fashion+Blog',
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'The Rise of Sustainable Fashion in Africa: Eco-Friendly Trends to Watch',
                'body' => 'As the global fashion industry grapples with its environmental impact, Africa is emerging as a leader in sustainable fashion practices. The continent\'s rich tradition of handcrafted textiles and natural materials provides a perfect foundation for eco-friendly fashion innovation.

Sustainable fashion in Africa isn\'t just a trend—it\'s a return to roots. For centuries, African communities have practiced what we now call "slow fashion," creating durable, timeless pieces using locally sourced materials and traditional techniques.

**Traditional Sustainable Practices:**

- **Natural Dyes**: Using plants, minerals, and other natural materials to create vibrant colors
- **Handweaving**: Time-honored techniques that produce unique, durable fabrics
- **Local Sourcing**: Utilizing materials available within communities, reducing transportation impact
- **Repair Culture**: Traditional practices of mending and altering garments to extend their lifespan

Modern African designers are building on these foundations, incorporating contemporary sustainable practices:

**Innovative Materials**: Designers are experimenting with organic cotton, hemp, bamboo fiber, and even innovative materials like fabric made from banana peels and pineapple leaves.

**Zero Waste Design**: Many African fashion houses are adopting zero-waste design principles, ensuring that every scrap of fabric is utilized in the production process.

**Fair Trade Practices**: Emphasis on fair wages and working conditions for artisans and garment workers, supporting local communities and preserving traditional skills.

**Upcycling and Recycling**: Creative reuse of discarded materials to create new fashion pieces, reducing waste and promoting circular economy principles.

The sustainable fashion movement in Africa is also being driven by conscious consumers who are increasingly aware of their environmental impact. Young Africans are choosing quality over quantity, investing in pieces that will last for years rather than following fast fashion trends.

**Key Players in African Sustainable Fashion:**

- Designers focusing on ethical production methods
- Artisan cooperatives preserving traditional crafts
- Social enterprises creating employment opportunities
- Tech innovators developing sustainable materials

The global fashion industry is taking notice of Africa\'s sustainable fashion innovations. International brands are partnering with African suppliers and designers to incorporate sustainable practices into their supply chains.

For consumers interested in sustainable African fashion, consider:
- Researching the brand\'s production methods and values
- Investing in quality pieces that will last
- Supporting local artisans and small businesses
- Learning about proper care to extend garment lifespan

The future of fashion is sustainable, and Africa is leading the way. By supporting sustainable African fashion brands, consumers can make a positive impact on both the environment and local communities while enjoying beautifully crafted, meaningful clothing.',
                'featured_image' => 'https://via.placeholder.com/800x600.png?text=Sustainable+Fashion',
                'published_at' => Carbon::now()->subDays(12),
            ],
            [
                'title' => 'Beauty Secrets from Africa: Natural Skincare Ingredients That Work',
                'body' => 'Africa is home to some of the world\'s most powerful natural skincare ingredients. For centuries, African women have used locally sourced plants, oils, and minerals to maintain healthy, glowing skin. These time-tested beauty secrets are now gaining recognition in the global beauty industry.

**Shea Butter (Karite)**
Perhaps the most famous African beauty export, shea butter comes from the nuts of the African shea tree. Rich in vitamins A and E, it provides deep moisturization and has anti-inflammatory properties. Traditionally used to protect skin from harsh sun and wind, shea butter is now a staple in premium skincare products worldwide.

**Argan Oil**
From the argan trees of Morocco, this "liquid gold" is packed with vitamin E, antioxidants, and essential fatty acids. Argan oil is incredibly versatile—it can be used on hair, face, and body. Its lightweight texture makes it perfect for all skin types, providing hydration without clogging pores.

**Baobab Oil**
Extracted from the seeds of the iconic baobab tree, this oil is rich in vitamins A, D, E, and F. It\'s quickly absorbed by the skin and provides excellent anti-aging benefits. Baobab oil helps improve skin elasticity and can even help fade scars and stretch marks.

**Marula Oil**
Native to Southern Africa, marula oil is lightweight yet deeply nourishing. It contains high levels of antioxidants and omega fatty acids, making it excellent for mature or damaged skin. The oil is also naturally antimicrobial, making it suitable for acne-prone skin.

**Black Soap (Dudu Osun)**
Traditional African black soap is made from plantain skins, palm oil, cocoa pods, and other natural ingredients. It\'s naturally antibacterial and antifungal, making it excellent for treating various skin conditions. Unlike harsh commercial soaps, black soap is gentle and moisturizing.

**Kigelia Africana**
Known as the sausage tree, kigelia produces fruits that have been used in traditional African medicine for centuries. The extract is known for its firming and toning properties, making it excellent for anti-aging skincare formulations.

**Hibiscus**
Often called "nature\'s botox," hibiscus flowers are rich in alpha-hydroxy acids (AHAs) that help exfoliate dead skin cells and promote cell regeneration. Hibiscus also contains vitamin C and antioxidants that help fight free radicals.

**Modern Applications:**

Today, these traditional ingredients are being incorporated into modern skincare formulations:

- **Cleansers**: Black soap-based cleansers for gentle yet effective cleansing
- **Moisturizers**: Shea butter and marula oil-based creams for deep hydration
- **Serums**: Baobab and argan oil serums for anti-aging benefits
- **Masks**: Hibiscus and other plant-based masks for exfoliation and brightening

**How to Incorporate African Beauty Ingredients:**

1. **Start Simple**: Begin with single-ingredient products like pure shea butter or argan oil
2. **Research Suppliers**: Look for fair-trade, ethically sourced ingredients
3. **Patch Test**: Always test new ingredients on a small area of skin first
4. **Be Consistent**: Natural ingredients work best with regular use over time

The beauty industry is finally recognizing what African women have known for generations—nature provides some of the most effective skincare ingredients. By incorporating these time-tested ingredients into your routine, you\'re not just caring for your skin; you\'re connecting with centuries of beauty wisdom.

As the clean beauty movement continues to grow, African natural ingredients are poised to play an increasingly important role in global skincare. Supporting brands that ethically source these ingredients helps ensure that the communities that have preserved this knowledge for generations benefit from its commercialization.',
                'featured_image' => 'https://via.placeholder.com/800x600.png?text=African+Beauty+Secrets',
                'published_at' => Carbon::now()->subDays(18),
            ],
            [
                'title' => 'Supporting Local Artisans: The Impact of Buying Handmade African Products',
                'body' => 'When you purchase handmade African products, you\'re not just buying an item—you\'re investing in communities, preserving cultural traditions, and supporting sustainable economic development. The impact of choosing handmade goes far beyond the individual transaction.

**Preserving Cultural Heritage**

African artisans are the guardians of centuries-old traditions. Every piece they create carries forward techniques, patterns, and stories passed down through generations. When you buy handmade:

- **Traditional Skills Survive**: Your purchase ensures that ancient crafts like weaving, beadwork, pottery, and metalworking continue to thrive
- **Cultural Stories Live On**: Each piece tells a story—of heritage, ritual, and community identity
- **Innovation Flourishes**: Artisans blend traditional techniques with contemporary designs, creating unique fusion pieces

**Economic Impact on Communities**

The economic benefits of supporting African artisans extend throughout entire communities:

**Direct Income**: Artisans earn fair wages for their skilled work, often significantly more than they would in other local employment options.

**Multiplier Effect**: When artisans earn income, they spend it locally—on food, education, healthcare, and other services—stimulating the broader local economy.

**Women\'s Empowerment**: Many artisan cooperatives focus on training and employing women, providing them with economic independence and leadership opportunities.

**Youth Engagement**: Craft traditions provide young people with valuable skills and economic opportunities, helping prevent urban migration and keeping communities intact.

**Environmental Benefits**

Handmade African products are inherently more sustainable than mass-produced alternatives:

- **Local Materials**: Artisans typically use locally sourced, natural materials, reducing transportation emissions
- **Minimal Waste**: Traditional production methods generate little waste compared to industrial processes
- **Durability**: Handmade products are built to last, reducing the need for frequent replacement
- **No Fast Fashion**: The time investment in handmade items naturally opposes throwaway culture

**Types of African Handmade Products to Support:**

**Textiles and Fashion:**
- Hand-woven fabrics and clothing
- Traditional and contemporary jewelry
- Leather goods and accessories
- Embroidered items

**Home Decor:**
- Pottery and ceramics
- Wooden sculptures and furniture
- Woven baskets and mats
- Metal artwork

**Art and Crafts:**
- Paintings and prints
- Musical instruments
- Carved items
- Beadwork

**How to Support Responsibly:**

**Research Your Sources:**
- Look for fair trade certifications
- Choose suppliers who work directly with artisans
- Verify that artisans receive fair compensation
- Support cooperatives and social enterprises

**Understand the Process:**
- Learn about the time and skill involved in creating each piece
- Appreciate the value of handmade work
- Share the stories behind the products with others

**Make Thoughtful Purchases:**
- Choose quality over quantity
- Buy pieces that you\'ll treasure and use
- Consider the cultural significance of items
- Respect traditional designs and their meanings

**The Ripple Effect of Your Purchase:**

When you buy from an African artisan cooperative, your money might:
- Send a child to school
- Provide healthcare for a family
- Fund community development projects
- Support women\'s empowerment programs
- Preserve endangered cultural practices

**Challenges Facing African Artisans:**

Despite the positive impact, African artisans face several challenges:
- Limited access to international markets
- Competition from mass-produced imitations
- Lack of business and marketing skills
- Limited access to credit and resources
- Infrastructure challenges

**How You Can Help Address These Challenges:**

- Support organizations that provide training and resources to artisans
- Share artisan stories on social media to increase awareness
- Encourage others to choose handmade
- Advocate for fair trade practices
- Consider visiting artisan communities when traveling

The global market for African handmade products is growing, driven by consumers who value authenticity, sustainability, and social impact. This growth represents an enormous opportunity for African communities to benefit from their cultural heritage and artistic skills.

By choosing handmade African products, you become part of a movement that values:
- Cultural preservation over cultural appropriation
- Fair trade over exploitation
- Sustainability over disposability
- Quality over quantity
- Community over corporation

Your purchasing decisions have power. Use that power to support African artisans and communities, and you\'ll own beautiful, meaningful products while making a positive impact on the world.',
                'featured_image' => 'https://via.placeholder.com/800x600.png?text=African+Artisans',
                'published_at' => Carbon::now()->subDays(25),
            ],
            [
                'title' => 'The Future of African E-commerce: Trends Shaping Online Shopping',
                'body' => 'African e-commerce is experiencing unprecedented growth, driven by increasing internet penetration, mobile phone adoption, and a young, tech-savvy population. The continent\'s e-commerce landscape is evolving rapidly, with unique innovations and solutions emerging to address local challenges and opportunities.

**Current State of African E-commerce**

The African e-commerce market is projected to reach $75 billion by 2025, representing tremendous growth from just a few billion dollars a decade ago. Several factors are driving this expansion:

**Mobile-First Approach**: With over 70% of Africans accessing the internet via mobile devices, African e-commerce platforms are designed with mobile users in mind from the ground up.

**Payment Innovation**: Mobile money solutions like M-Pesa, digital wallets, and innovative payment platforms are solving the challenge of limited traditional banking access.

**Logistics Solutions**: Companies are developing creative solutions for last-mile delivery, including partnerships with local motorcycle taxis (boda-boda) and community pickup points.

**Key Trends Shaping the Future:**

**1. Social Commerce Integration**
Social media platforms are becoming shopping destinations, with features like:
- Instagram and Facebook shopping integration
- WhatsApp Business for customer service and sales
- Live streaming shopping events
- Influencer-driven commerce

**2. Cross-Border Trade Facilitation**
The African Continental Free Trade Area (AfCFTA) is opening up opportunities for:
- Inter-African trade growth
- Simplified customs procedures
- Regional e-commerce platforms
- Currency harmonization initiatives

**3. Artificial Intelligence and Personalization**
AI is being deployed to:
- Provide personalized product recommendations
- Optimize inventory management
- Improve customer service with chatbots
- Enhance fraud detection and security

**4. Sustainable and Local Focus**
Consumers are increasingly interested in:
- Locally made products
- Sustainable and eco-friendly options
- Supporting local businesses and artisans
- Transparent supply chains

**5. Fintech Integration**
Financial technology innovations include:
- Buy-now-pay-later services
- Digital lending for small businesses
- Cryptocurrency payment options
- Micro-investment platforms

**Challenges and Solutions:**

**Infrastructure Challenges:**
- Limited internet connectivity in rural areas
- Power outages affecting operations
- Poor road networks for delivery

**Solutions Being Developed:**
- Satellite internet expansion
- Solar-powered infrastructure
- Drone delivery pilots
- Community-based pickup networks

**Trust and Security:**
- Building consumer confidence in online transactions
- Protecting against fraud
- Ensuring data privacy

**Solutions:**
- Secure payment gateways
- Buyer protection programs
- Transparent review systems
- Regulatory compliance frameworks

**Industry-Specific Growth Areas:**

**Fashion and Beauty**: Local designers and beauty brands are finding global audiences through e-commerce platforms.

**Agriculture**: Farmers are connecting directly with consumers and retailers through digital platforms.

**Education**: Online learning platforms and digital educational resources are expanding rapidly.

**Healthcare**: Telemedicine and online pharmacy services are improving access to healthcare.

**Success Stories and Innovations:**

African e-commerce companies are developing innovative solutions:
- Drone delivery services in Rwanda
- Solar-powered internet kiosks in Kenya
- Mobile money integration in West Africa
- Community-based delivery networks in Nigeria

**The Role of Startups and Innovation Hubs:**

African tech hubs in cities like Lagos, Nairobi, Cape Town, and Cairo are fostering innovation:
- Incubating e-commerce startups
- Developing local solutions for local problems
- Attracting international investment
- Building tech talent capacity

**Consumer Behavior Trends:**

**Mobile Shopping**: Consumers prefer mobile apps over desktop websites
**Social Proof**: Reviews and recommendations heavily influence purchases
**Convenience**: Same-day or next-day delivery is increasingly expected
**Local Content**: Content in local languages increases engagement
**Community**: Group buying and social shopping features are popular

**Future Predictions:**

**Next 5 Years (2025-2030):**
- Voice commerce integration
- Augmented reality shopping experiences
- Expanded rural e-commerce penetration
- Greater inter-African trade online
- Advanced logistics automation

**Government Support and Policy:**

African governments are supporting e-commerce growth through:
- Digital identity initiatives
- E-commerce regulation frameworks
- Investment in digital infrastructure
- Support for digital skills training

**Opportunities for Businesses:**

**Local Businesses**: E-commerce provides access to wider markets and growth opportunities
**International Companies**: African markets offer significant growth potential
**Investors**: High returns on investment in African e-commerce ventures
**Consumers**: Access to wider product selection and competitive prices

**The Path Forward:**

The future of African e-commerce is bright, with young entrepreneurs leading innovation and creating solutions tailored to local needs. Success will come from:
- Understanding local market dynamics
- Building trust with consumers
- Developing sustainable business models
- Investing in technology and infrastructure
- Creating inclusive platforms that serve all segments of society

African e-commerce is not just about replicating global models—it\'s about creating uniquely African solutions that address local challenges while connecting the continent to global opportunities. The next decade will be transformative for African commerce, with e-commerce playing a central role in the continent\'s economic development.',
                'featured_image' => 'https://via.placeholder.com/800x600.png?text=African+E-commerce',
                'published_at' => Carbon::now()->subDays(30),
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::create([
                'title' => $blog['title'],
                'slug' => Str::slug($blog['title']),
                'body' => $blog['body'],
                'featured_image' => $blog['featured_image'],
                'published_at' => $blog['published_at'],
                'admin_id' => $admin ? $admin->id : null,
            ]);
        }

        echo "Created " . count($blogs) . " blog posts.\n";
    }
}