<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prof. Ahmed Omar - Mathematics Expert</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .profile-card {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
            position: relative;
        }

        .header {
            background: linear-gradient(to right, #2c3e50, #4a6491);
            color: white;
            padding: 30px 25px;
            text-align: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 40px;
            background: white;
            border-radius: 50% 50% 0 0;
        }

        .name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .title {
            font-size: 16px;
            font-weight: 300;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .experience-section {
            padding: 40px 25px 25px;
            text-align: left;
        }

        .experience-item {
            margin-bottom: 25px;
            padding-left: 20px;
            border-left: 3px solid #3498db;
        }

        .experience-years {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .experience-desc {
            font-size: 15px;
            color: #555;
            line-height: 1.5;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #ddd, transparent);
            margin: 30px 0;
        }

        .join-section {
            text-align: center;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 15px;
            margin: 0 25px 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .join-title {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .join-button {
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 14px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 15px;
            width: 100%;
        }

        .join-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }

        .join-button i {
            margin-left: 10px;
            font-size: 16px;
        }

        .students-text {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 600;
        }

        .contact-section {
            padding: 25px;
            background-color: #2c3e50;
            color: white;
            text-align: center;
        }

        .contact-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .contact-detail {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .contact-detail i {
            margin-right: 10px;
            color: #3498db;
            width: 20px;
        }

        .phone-numbers {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .phone-number {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .phone-number i {
            margin-right: 8px;
            color: #2ecc71;
        }

        .location-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(5px);
        }

        @media (max-width: 480px) {
            .profile-card {
                max-width: 100%;
            }
            
            .phone-numbers {
                flex-direction: column;
                gap: 10px;
            }
            
            .name {
                font-size: 24px;
            }
            
            .experience-item {
                padding-left: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-card">
        <div class="header">
            <div class="location-badge">
                <i class="fas fa-map-marker-alt"></i> Asyute
            </div>
            <h1 class="name">Prof. Ahmed Omar</h1>
            <p class="title">Mathematics Expert & Educator</p>
        </div>
        
        <div class="experience-section">
            <div class="experience-item">
                <div class="experience-years">More than 15 years experience</div>
                <div class="experience-desc">American Diploma Mathematics<br>SAT-ACT-EST Preparation</div>
            </div>
            
            <div class="experience-item">
                <div class="experience-years">More than 40 years experience</div>
                <div class="experience-desc">University Mathematics Teaching</div>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="join-section">
            <div class="join-title">Join Now</div>
            <button class="join-button">
                Join Now <i class="fas fa-arrow-right"></i>
            </button>
            <div class="students-text">Students</div>
        </div>
        
        <div class="contact-section">
            <div class="contact-name">Prof. Ahmed Omar</div>
            
            <div class="contact-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span>On site courses in Asyute city only</span>
            </div>
            
            <div class="contact-detail">
                <i class="fas fa-laptop-house"></i>
                <span>Online courses on other locations</span>
            </div>
            
            <div class="phone-numbers">
                <div class="phone-number">
                    <i class="fas fa-phone"></i>
                    <span>+201060509026</span>
                </div>
                <div class="phone-number">
                    <i class="fas fa-phone"></i>
                    <span>+201023560301</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add interactivity to the join button
        document.querySelector('.join-button').addEventListener('click', function() {
            alert('Thank you for your interest! You will be redirected to the registration page.');
            // In a real implementation, you would redirect to a registration page
            // window.location.href = '/register';
        });
        
        // Add a subtle animation to the card on load
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.profile-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>