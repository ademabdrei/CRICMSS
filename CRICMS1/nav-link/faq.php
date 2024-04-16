<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nekemte City Resident ID Card Management System - FAQ</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <style>
        /* Custom CSS */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .faq-item {
            background-color: #fff;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .question {
            font-size: 15px;
            font-weight: bold;
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.3s ease;
        }

        .answer {
            padding: 10px;
            display: none;
        }

        .answer.show {
            display: block;
        }

        .question-icon {
            float: right;
        }

        .search-form {
            margin-bottom: 10px;
        }

        .sort-links {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Nekemte City Resident ID Card FAQ</h1>

        <!-- Search Form -->
        <form action="" method="GET" class="search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search FAQ" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <?php
        // Define the FAQ questions and answers
        $faqs = array(
            array(
                "question" => "What is the purpose of the Nekemte City Resident ID Card?",
                "answer" => "The Nekemte City Resident ID Card is an official identification document that serves as proof of residency for individuals living within the Nekemte city limits. It allows residents to access various city services, programs, and benefits."
            ),
            array(
                "question" => "Who is eligible to apply for the Nekemte City Resident ID Card?",
                "answer" => "Any person residing within the boundaries of Nekemte city is eligible to apply for the Nekemte City Resident ID Card. Applicants must provide documentation to verify their residency, such as a utility bill, rental agreement, or other official proof of address."
            ),
            array(
                "question" => "How do I apply for the Nekemte City Resident ID Card?",
                "answer" => "To apply for the Nekemte City Resident ID Card, you can visit the Nekemte City Administration office and submit a completed application form, along with the required supporting documents. The application process may also be available online or at designated service centers. You will need to provide personal information, proof of residency, and a photograph for the card."
            ),
            array(
                "question" => "What are the benefits of having a Nekemte City Resident ID Card?",
                "answer" => "The Nekemte City Resident ID Card offers several benefits to residents, including:
        - Access to city-sponsored programs, services, and facilities
        - Discounts and special offers from local businesses
        - Ability to participate in community events and activities
        - Proof of residency for various purposes within the city"
            ),
            array(
                "question" => "How long is the Nekemte City Resident ID Card valid?",
                "answer" => "The Nekemte City Resident ID Card is valid for a period of 5 years from the date of issuance. Cardholders are required to renew their cards before the expiration date to maintain their residency status and continue accessing city services and benefits."
            ),
            array(
                "question" => "What should I do if I lose my Nekemte City Resident ID Card?",
                "answer" => "If you lose your Nekemte City Resident ID Card, you should report the loss to the Nekemte City Administration as soon as possible. You can then apply for a replacement card, which may require a small fee and the submission of additional documentation to verify your identity and residency status."
            ),
            array(
                "question" => "Can I use my Nekemte City Resident ID Card outside of the city?",
                "answer" => "The Nekemte City Resident ID Card is primarily designed for use within the Nekemte city limits. While it may be accepted as a form of identification in some other areas, its primary purpose is to provide access to city services and programs. It is not a nationally recognized form of identification for use outside of Nekemte."
            ),
            array(
                "question" => "How can I update the information on my Nekemte City Resident ID Card?",
                "answer" => "If you need to update the information on your Nekemte City Resident ID Card, such as a change of address or name, you can visit the Nekemte City Administration office and submit the necessary documentation to request a card update. You may be required to pay a small fee for the update process."
            ),
            array(
                "question" => "Is there a fee for applying for the Nekemte City Resident ID Card?",
                "answer" => "Yes, there is a nominal fee for applying for the Nekemte City Resident ID Card. The current application fee is 50 ETB (Ethiopian Birr). This fee covers the cost of processing the application and producing the physical card. The fee may be subject to change, so it's best to check with the Nekemte City Administration for the most up-to-date information."
            ),
            array(
                "question" => "Can I use my Nekemte City Resident ID Card to vote in local elections?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used as a valid form of identification for voting in local elections within the Nekemte city limits. The card serves as proof of residency and allows you to participate in the democratic process at the municipal level."
            ),
            array(
                "question" => "Is the Nekemte City Resident ID Card transferable to other family members?",
                "answer" => "No, the Nekemte City Resident ID Card is non-transferable and is issued specifically to the individual applicant. Each resident must apply for and obtain their own Nekemte City Resident ID Card, as it is tied to their personal information and residency status."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used as a travel document?",
                "answer" => "No, the Nekemte City Resident ID Card is not a valid travel document and cannot be used for international or domestic travel. It is a city-issued identification card and is not recognized for travel purposes outside of the Nekemte city limits."
            ),
            array(
                "question" => "What should I do if I move out of Nekemte city?",
                "answer" => "If you move out of the Nekemte city limits, you are no longer eligible to hold a Nekemte City Resident ID Card. You should inform the Nekemte City Administration about your change of residency and return the card to the issuing authority. This will ensure that the city's records are up-to-date and that your access to city services and benefits is terminated."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to access government services outside of the city?",
                "answer" => "No, the Nekemte City Resident ID Card is primarily designed for use within the Nekemte city limits and may not be recognized or accepted by government agencies or service providers outside of the city. It is not a national-level identification document and has limited use beyond the Nekemte city boundaries."
            ),
            array(
                "question" => "Is there a way to track the status of my Nekemte City Resident ID Card application?",
                "answer" => "Yes, the Nekemte City Administration offers a way for applicants to track the status of their Nekemte City Resident ID Card application. You can either visit the administration office in person or check the status online through the city's website by providing your application reference number or other identifying information."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used for banking or financial transactions?",
                "answer" => "No, the Nekemte City Resident ID Card is not a financial instrument and cannot be used for banking or financial transactions. It is solely an identification document for the purpose of accessing city services and programs. For any banking or financial needs, you will need to use a nationally recognized form of identification, such as a national ID card or passport."
            ),
            array(
                "question" => "What should I do if I notice an error on my Nekemte City Resident ID Card?",
                "answer" => "If you notice any errors or inaccuracies on your Nekemte City Resident ID Card, such as a misspelled name or incorrect address, you should contact the Nekemte City Administration as soon as possible. They will guide you through the process of requesting a correction or replacement card to ensure your information is accurate and up-to-date."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to access healthcare services in the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used to access healthcare services provided by the Nekemte City Health Department or affiliated healthcare facilities. The card serves as proof of residency and allows you to receive city-subsidized or discounted healthcare services as a Nekemte resident."
            ),
            array(
                "question" => "Is the Nekemte City Resident ID Card mandatory for all city residents?",
                "answer" => "No, the Nekemte City Resident ID Card is not mandatory for all residents of Nekemte. However, it is highly recommended as it provides access to a variety of city services, programs, and benefits. While not compulsory, having a valid Nekemte City Resident ID Card can be advantageous for residents in their day-to-day interactions with the city administration."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to access educational institutions within the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used to access educational institutions and facilities within the city, such as public schools and municipal libraries. The card may be required for enrollment, student discounts, and access to certain city-sponsored educational programs."
            ),
            array(
                "question" => "How often do I need to renew my Nekemte City Resident ID Card?",
                "answer" => "The Nekemte City Resident ID Card is valid for a period of 5 years from the date of issuance. Cardholders are required to renew their cards before the expiration date to maintain their residency status and continue accessing city services and benefits. The renewal process involves submitting an application and updated documentation to the Nekemte City Administration."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to register a vehicle in the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used as proof of residency when registering a vehicle within the Nekemte city limits. This allows residents to comply with local registration requirements and access city-specific vehicle services and programs."
            ),
            array(
                "question" => "Is there a way to replace a damaged or worn-out Nekemte City Resident ID Card?",
                "answer" => "Yes, if your Nekemte City Resident ID Card is damaged or worn out, you can apply for a replacement card. You will need to visit the Nekemte City Administration office, submit an application for a replacement, and provide any required documentation. There may be a small fee associated with the replacement process."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to open a bank account in the city?",
                "answer" => "No, the Nekemte City Resident ID Card is not a valid form of identification for opening a bank account. Financial institutions typically require nationally recognized forms of identification, such as a national ID card or passport, for banking and financial services. The Nekemte City Resident ID Card is not accepted for these purposes."
            ),
            array(
                "question" => "Does the Nekemte City Resident ID Card provide any tax benefits or exemptions for residents?",
                "answer" => "The Nekemte City Resident ID Card does not directly provide any tax benefits or exemptions for its holders. However, the card may be used as proof of residency when applying for certain city-specific tax relief programs or initiatives that are available to Nekemte residents. The specific tax-related benefits may vary, and residents should consult with the Nekemte City Administration for more details."
            ),
            array(
                "question" => "Can I use my Nekemte City Resident ID Card to apply for a driver's license in the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used as proof of residency when applying for a driver's license within the Nekemte city limits. The card demonstrates that you meet the local residency requirements for obtaining a driver's license in the city."
            ),
            array(
                "question" => "Is there a way to check the remaining validity of my Nekemte City Resident ID Card?",
                "answer" => "Yes, you can check the remaining validity of your Nekemte City Resident ID Card by contacting the Nekemte City Administration. They can provide you with information on the expiration date of your card and the steps you need to take for renewal, if applicable."
            ),
            array(
                "question" => "Can I use my Nekemte City Resident ID Card to apply for government assistance programs in the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used as proof of residency when applying for city-sponsored government assistance programs, such as social welfare, housing, or employment support initiatives. The card demonstrates your eligibility as a Nekemte resident to access these city-level services and benefits."
            ),
            array(
                "question" => "What should I do if my Nekemte City Resident ID Card is stolen or lost?",
                "answer" => "If your Nekemte City Resident ID Card is stolen or lost, you should report the incident to the Nekemte City Administration as soon as possible. They will guide you through the process of cancelling the lost or stolen card and applying for a replacement. This is important to prevent unauthorized use of your card and to maintain the integrity of your residency status in the city."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to access public transportation within the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used to access and pay for public transportation services within the Nekemte city limits. This includes buses, commuter trains, and other municipal transportation options. The card may also be eligible for discounted fares or special passes reserved for Nekemte residents."
            ),
            array(
                "question" => "Is there a way to check the status of my Nekemte City Resident ID Card application online?",
                "answer" => "Yes, the Nekemte City Administration has an online portal where residents can check the status of their Nekemte City Resident ID Card application. By providing your application reference number or other identifying information, you can track the progress of your application and receive updates on its processing."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to access city parks, recreational facilities, and cultural events?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used to access various city-owned parks, recreational facilities, and cultural events. The card may provide discounted admission or special access privileges for Nekemte residents, allowing them to fully participate in the city's community activities and programs."
            ),
            array(
                "question" => "Is there a way to dispute or appeal the rejection of my Nekemte City Resident ID Card application?",
                "answer" => "If your Nekemte City Resident ID Card application is rejected, you have the right to dispute or appeal the decision. You can contact the Nekemte City Administration to understand the reasons for the rejection and learn about the appeal process. They will guide you through the necessary steps to address any issues or provide additional documentation to support your application."
            ),
            array(
                "question" => "Can the Nekemte City Resident ID Card be used to access senior or disability-related services in the city?",
                "answer" => "Yes, the Nekemte City Resident ID Card can be used to access senior or disability-related services and programs provided by the Nekemte City Administration. These may include discounted transportation, specialized healthcare services, social welfare initiatives, and other city-level support for senior citizens and individuals with disabilities who reside within the Nekemte city limits."
            )
        );

        // Functionality 1: Search Functionality
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $filteredFaqs = array_filter($faqs, function ($faq) use ($searchTerm) {
            return stripos($faq['question'], $searchTerm) !== false || stripos($faq['answer'], $searchTerm) !== false;
        });

        // Pagination
        $faqsPerPage = 5;
        $totalFaqs = count($filteredFaqs);
        $totalPages = ceil($totalFaqs / $faqsPerPage);
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $faqsPerPage;
        $paginatedFaqs = array_slice($filteredFaqs, $offset, $faqsPerPage);

        // Display the filtered FAQ questions and answers
        foreach ($paginatedFaqs as $faq) : ?>
            <div class="faq-item">
                <div class="question" onclick="toggleAnswer(this)">
                    <?= htmlspecialchars($faq["question"]) ?>
                    <i class="fas fa-plus question-icon"></i>
                </div>
                <div class="answer">
                    <?= htmlspecialchars($faq["answer"]) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($totalPages > 1) : ?>
                    <?php if ($currentPage > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($searchTerm) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>">Next</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php include '../includes/footer.php'; ?>
<!-- Bootstrap JavaScript dependencies -->
<script src="../js/jquery-3.5.1.slim.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script>
        function toggleAnswer(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('.question-icon');
            answer.classList.toggle('show');
            icon.classList.toggle('fa-plus');
            icon.classList.toggle('fa-minus');
        }
    </script>
</body>

</html>