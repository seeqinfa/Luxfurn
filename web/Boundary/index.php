<?php
// index.php
require_once '../header.php';
?>

<main style="padding: 80px; max-width: 1200px; margin: auto; font-family: Arial, sans-serif;">
    <section style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Welcome to LuxFurn</h1>
        <p style="font-size: 1.2rem; color: #555;">
            Where luxury meets comfort – explore our premium furniture collection and transform your home today.
        </p>
    </section>

    <section style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
        <div style="flex: 1 1 300px; max-width: 350px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <img src="/FYP-25-S2-34-Chatbot/Src/img/livingroom.jpg" alt="Living Room Furniture" style="width: 100%; border-radius: 10px;">
            <h3 style="margin-top: 15px;padding-top: 60px;">Living Room</h3>
            <p style="color: #666;">Discover stylish sofas, coffee tables, and TV consoles to create your dream living space.</p>
        </div>
        <div style="flex: 1 1 300px; max-width: 350px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <img src="/FYP-25-S2-34-Chatbot/Src/img/bedroom.jpg" alt="Bedroom Furniture" style="width: 100%; border-radius: 10px;">
            <h3 style="margin-top: 15px;">Bedroom</h3>
            <p style="color: #666;">From cozy bed frames to spacious wardrobes, design a bedroom that’s both functional and luxurious.</p>
        </div>
        <div style="flex: 1 1 300px; max-width: 350px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <img src="/FYP-25-S2-34-Chatbot/Src/img/dining.jpg" alt="Dining Furniture" style="width: 100%; border-radius: 10px;">
            <h3 style="margin-top: 15px;">Dining</h3>
            <p style="color: #666;">Upgrade your dining experience with our elegant tables, chairs, and storage solutions.</p>
        </div>
    </section>

    <section style="margin-top: 60px; text-align: center;">
        <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/viewFurnitureUI.php" 
           style="background: #e67e22; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-size: 1.1rem;">
           Shop Now
        </a>
    </section>
</main>

<?php
// Footer include if available
?>
