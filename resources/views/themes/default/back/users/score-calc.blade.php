@extends('themes.default.layouts.back.student-master')

@section('title')
    حاسبة النقاط - Score Calculator
@endsection

@section('css')
    {{-- <style>
        .score-calc-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px 0;
        }

        .calc-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }

        .calc-section {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .calc-section h4 {
            color: #1e40af;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }

        .input-group input[type="number"], .input-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: white;
        }

        .input-group input[type="number"]:focus, .input-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group select {
            cursor: pointer;
        }

        .result-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            text-align: center;
        }

        .result-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .calc-btn {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .calc-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        .formula-display {
            background: #eff6ff;
            border: 2px solid #bfdbfe;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #1e40af;
        }

        .row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .col-md-6 {
            flex: 1;
            min-width: 300px;
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }

            .col-md-6 {
                min-width: auto;
            }
        }
    </style> --}}
@endsection

@section('content')
    {{-- <div class="main-content">
        <div class="calc-header">
            <h1 style="color: #1e40af; font-size: 2rem; margin-bottom: 10px;">Score & GPA Calculator</h1>
        </div>

        <div class="score-calc-container" style="max-width: 500px; margin: 0 auto;">
            <!-- Score Category -->
            <div class="input-group">
                <label for="score_category">Select Score Category:</label>
                <select id="score_category" onchange="updateScoreOptions()">
                    <option value="dsat_est2">DSAT / EST II</option>
                    <option value="dsat_act2">DSAT / ACT II</option>
                    <option value="est1_est2">EST I / EST II</option>
                    <option value="est1_act2">EST I / ACT II</option>
                    <option value="act1_act2">ACT I / ACT II</option>
                    <option value="act1_est2">ACT I / EST II</option>
                </select>
            </div>

            <!-- Institution Type -->
            <div class="input-group">
                <label for="institution_type">Institution Type:</label>
                <select id="institution_type">
                    <option value="government">Government Universities</option>
                    <option value="nonprofit">Non-Profit Private Universities</option>
                    <option value="private">Private Universities</option>
                </select>
            </div>

            <!-- Score 1 -->
            <div class="input-group">
                <label for="score1" id="score1_label">Score 1 (out of 1600):</label>
                <input type="number" id="score1" placeholder="Enter first score (e.g., DSAT or EST I or ACT I)" min="0" max="1600" step="0.1">
            </div>

            <!-- Score 2 -->
            <div class="input-group">
                <label for="score2" id="score2_label">Score 2 (out of 1600):</label>
                <input type="number" id="score2" placeholder="Enter second score (e.g., EST II or ACT II)" min="0" max="1600" step="0.1">
            </div>

            <!-- GPA -->
            <div class="input-group">
                <label for="gpa">GPA (out of 4.0):</label>
                <input type="number" id="gpa" placeholder="Enter GPA" min="0" max="4.0" step="0.01">
            </div>

            <!-- Calculate Button -->
            <button class="calc-btn" onclick="calculateScore()">
                Calculate
            </button>

            <!-- Results -->
            <div class="result-section" id="result_section" style="display: none;">
                <h5>Final Score</h5>
                <div class="result-value" id="final_score">0</div>
                <div id="score_breakdown"></div>
            </div>

            <!-- Formula Display -->
            <div class="formula-display" id="formula_display" style="display: none;">
                <strong>Formula Used:</strong><br>
                <span id="formula_text"></span>
            </div>
        </div>
    </div> --}}
    <div class="container" style="max-width:720px; background:#fff; padding:50px 40px; margin:auto; border-radius:20px; box-shadow:0 12px 35px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <h3 style="text-align:center; margin-bottom:40px; font-size:30px; color:#0d47a1;">Score & GPA Calculator</h3>

        <label for="category" style="font-size: 18px; color: #0d47a1;">Select Score Category:</label>
        <select id="category" style="width:100%; padding:16px; margin-top:5px; margin-bottom:15px; font-size:18px; border:1px solid #ccc; border-radius:10px;">
            <option value="dsat_est2">DSAT / EST II</option>
            <option value="dsat_act2">DSAT / ACT II</option>
            <option value="est1_est2">EST I / EST II</option>
            <option value="est1_act2">EST I / ACT II</option>
            <option value="act1_act2">ACT I / ACT II</option>
            <option value="act1_est2">ACT I / EST II</option>
        </select>

        <label for="type" style="font-size: 18px; color: #0d47a1;">Institution Type:</label>
        <select id="type" style="width:100%; padding:16px; margin-top:5px; margin-bottom:15px; font-size:18px; border:1px solid #ccc; border-radius:10px;">
            <option value="gov">Government Universities</option>
            <option value="ahly">Non-Profit Private Universities</option>
            <option value="khas">Private Universities</option>
        </select>

        <label for="score1" style="font-size: 18px; color: #0d47a1;">Score 1 (out of 1600):</label>
        <input type="number" id="score1" placeholder="Enter first score (e.g., DSAT or EST I or ACT I)" style="width:100%; padding:16px; margin-top:5px; margin-bottom:15px; font-size:18px; border:1px solid #ccc; border-radius:10px;" />

        <label for="score2" style="font-size: 18px; color: #0d47a1;">Score 2 (out of 1600):</label>
        <input type="number" id="score2" placeholder="Enter second score (e.g., EST II or ACT II)" style="width:100%; padding:16px; margin-top:5px; margin-bottom:15px; font-size:18px; border:1px solid #ccc; border-radius:10px;" />

        <label for="gpa" style="font-size: 18px; color: #0d47a1;">GPA (out of 4.0):</label>
        <input type="number" step="0.01" id="gpa" placeholder="Enter GPA" style="width:100%; padding:16px; margin-top:5px; margin-bottom:15px; font-size:18px; border:1px solid #ccc; border-radius:10px;" />

        <button onclick="calculateScore()" style="margin-top:35px; width:100%; background:linear-gradient(to right, #1565c0, #0d47a1); color:white; padding:18px; font-size:24px; border:none; border-radius:12px; cursor:pointer; transition: background 0.3s ease;">
            Calculate
        </button>

        <div id="result" style="margin-top:40px; text-align:center; font-size:28px; color:#2e7d32; font-weight:bold; background-color:#e8f5e9; padding:25px; border-radius:12px; border:2px dashed #a5d6a7;">
        </div>
    </div>

    <script>
        function calculateScore() {
            const score1 = parseFloat(document.getElementById("score1").value);
            const score2 = parseFloat(document.getElementById("score2").value);
            const gpa = parseFloat(document.getElementById("gpa").value);
            const type = document.getElementById("type").value;

            if (isNaN(score1) || isNaN(score2) || isNaN(gpa)) {
                document.getElementById("result").innerText = "Please fill all fields correctly.";
                return;
            }

            let weight1 = 0, weight2 = 0, weightGpa = 0;

            // Score 1 weights
            if (type === "gov") {
                if (score1 >= 1090) {
                    weight1 = (score1 / 1600) * 69;
                } else {
                    weight1 = (score1 / 1600) * 60;
                }
            } else {
                if (score1 >= 1090) {
                    weight1 = (score1 / 1600) * 75;
                } else {
                    weight1 = (score1 / 1600) * 60;
                }
            }

            // Score 2 weight always 15%
            weight2 = (score2 / 1600) * 15;

            // GPA out of 40 directly (not percentage based)
            weightGpa = gpa;

            const total = (weight1 + weight2 + weightGpa).toFixed(4);

            document.getElementById("result").innerHTML = `<strong>Total Weighted Score:</strong> ${total}`;
        }
    </script>

@endsection

@section('js')
    {{-- <script>
        // Update score labels based on selected category
        function updateScoreOptions() {
            const category = document.getElementById('score_category').value;
            const score1Label = document.getElementById('score1_label');
            const score2Label = document.getElementById('score2_label');
            const score1Input = document.getElementById('score1');
            const score2Input = document.getElementById('score2');

            let labels = {
                'dsat_est2': ['DSAT Score (out of 1600):', 'EST II Score (out of 1600):'],
                'dsat_act2': ['DSAT Score (out of 1600):', 'ACT II Score (out of 1600):'],
                'est1_est2': ['EST I Score (out of 1600):', 'EST II Score (out of 1600):'],
                'est1_act2': ['EST I Score (out of 1600):', 'ACT II Score (out of 1600):'],
                'act1_act2': ['ACT I Score (out of 1600):', 'ACT II Score (out of 1600):'],
                'act1_est2': ['ACT I Score (out of 1600):', 'EST II Score (out of 1600):']
            };

            let placeholders = {
                'dsat_est2': ['Enter DSAT score', 'Enter EST II score'],
                'dsat_act2': ['Enter DSAT score', 'Enter ACT II score'],
                'est1_est2': ['Enter EST I score', 'Enter EST II score'],
                'est1_act2': ['Enter EST I score', 'Enter ACT II score'],
                'act1_act2': ['Enter ACT I score', 'Enter ACT II score'],
                'act1_est2': ['Enter ACT I score', 'Enter EST II score']
            };

            score1Label.textContent = labels[category][0];
            score2Label.textContent = labels[category][1];
            score1Input.placeholder = placeholders[category][0];
            score2Input.placeholder = placeholders[category][1];

            // Clear previous results
            document.getElementById('result_section').style.display = 'none';
            document.getElementById('formula_display').style.display = 'none';
        }

                function calculateScore() {
            const scoreCategory = document.getElementById('score_category').value;
            const institutionType = document.getElementById('institution_type').value;
            const score1 = parseFloat(document.getElementById('score1').value) || 0;
            const score2 = parseFloat(document.getElementById('score2').value) || 0;
            const gpa = parseFloat(document.getElementById('gpa').value) || 0;

            if (score1 === 0 || score2 === 0 || gpa === 0) {
                alert('Please fill in all fields');
                return;
            }

            /**
             * Correct Formula Based on Original Image:
             * Government: (Total ÷ 1600) × 471 + (Est2 ÷ 1600) × 2 + (Act2 ÷ 1600) × 2 + GPA(401)
             * Private: (Total ÷ 1600) × 751 + (Est2 ÷ 1600) × 2 + (Act2 ÷ 1600) × 2 + GPA(401)
             *
             * The formula shows:
             * 1. Total score (first major test) gets the main multiplier (471/601/751)
             * 2. Est2 and Act2 always get multiplier of 2 regardless of which is second
             * 3. GPA contributes fixed 401 points
             */

            // GPA calculation: Convert GPA to points out of 401 maximum
            // If GPA is 4.0, it contributes 401 points. If GPA is 3.0, it contributes (3.0/4.0)*401 = 300.75 points
            const gpaPoints = (gpa / 4.0) * 401;

            // Determine the main score multiplier based on institution type
            let mainMultiplier;

            if (institutionType === 'government') {
                mainMultiplier = 471;
            } else if (institutionType === 'nonprofit') {
                // Based on the pattern, nonprofit should be between government and private
                mainMultiplier = 601;
            } else { // private
                mainMultiplier = 751;
            }

            // Calculate scores based on the test type
            let score1Points, score2Points;

            // According to the original image:
            // - The main test (DSAT, EST I, ACT I) gets the main multiplier
            // - EST2 and ACT2 always get multiplier of 2

            const [test1, test2] = scoreCategory.split('_');

            // First score calculation
            if (test1 === 'dsat' || test1 === 'est1' || test1 === 'act1') {
                // Main test gets the institution multiplier
                score1Points = (score1 / 1600) * mainMultiplier;
            } else {
                // Fallback (shouldn't happen with current options)
                score1Points = (score1 / 1600) * mainMultiplier;
            }

            // Second score calculation
            if (test2 === 'est2' || test2 === 'act2') {
                // EST2 and ACT2 always get multiplier of 2
                score2Points = (score2 / 1600) * 2;
            } else {
                // Fallback for other combinations
                score2Points = (score2 / 1600) * 2;
            }

            // Final score
            const finalScore = score1Points + score2Points + gpaPoints;

            // Display results
            document.getElementById('final_score').textContent = finalScore.toFixed(2);

            const breakdown = `
                <div style="text-align: left; margin-top: 15px; font-size: 14px;">
                    <div>Score 1 Points: ${score1Points.toFixed(2)}</div>
                    <div>Score 2 Points: ${score2Points.toFixed(2)}</div>
                    <div>GPA Points: ${gpaPoints.toFixed(2)}</div>
                    <hr style="margin: 10px 0;">
                    <div><strong>Total: ${finalScore.toFixed(2)}</strong></div>
                </div>
            `;

            document.getElementById('score_breakdown').innerHTML = breakdown;
            document.getElementById('result_section').style.display = 'block';

                        // Update formula display
            const categoryText = scoreCategory.toUpperCase().replace('_', ' / ');
            const institutionText = institutionType.charAt(0).toUpperCase() + institutionType.slice(1);

            document.getElementById('formula_text').innerHTML = `
                <strong>${categoryText}</strong> for <strong>${institutionText} Universities</strong><br>
                Formula: (${test1.toUpperCase()} ÷ 1600) × ${mainMultiplier} + (${test2.toUpperCase()} ÷ 1600) × 2 + (GPA ÷ 4.0) × 401<br>
                Calculation: (${score1} ÷ 1600) × ${mainMultiplier} + (${score2} ÷ 1600) × 2 + (${gpa} ÷ 4.0) × 401<br>
                = ${score1Points.toFixed(2)} + ${score2Points.toFixed(2)} + ${gpaPoints.toFixed(2)} = <strong>${finalScore.toFixed(2)}</strong>
            `;
            document.getElementById('formula_display').style.display = 'block';
        }

        // Auto-calculate when all fields are filled
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="number"], select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const score1 = document.getElementById('score1').value;
                    const score2 = document.getElementById('score2').value;
                    const gpa = document.getElementById('gpa').value;

                    if (score1 && score2 && gpa) {
                        calculateScore();
                    }
                });
            });

            // Initialize labels
            updateScoreOptions();
        });
    </script> --}}
@endsection
