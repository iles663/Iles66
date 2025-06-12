<?php
session_start();
require_once 'db_connection.php';
// Function to get user by ID
function getUserById($id) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;
if ($isLoggedIn) {
    $user = getUserById($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WalletChat | Digital Wallet & Messaging</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: gold;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #FFD700, #D4AF37);
        }
        .chat-bubble {
            border-radius: 1.25rem;
            max-width: 80%;
        }
        .chat-bubble.sent {
            border-bottom-right-radius: 0;
            background-color: #6e8efb;
            color: white;
        }
        .chat-bubble.received {
            border-bottom-left-radius: 0;
            background-color: #f1f1f1;
        }
        .wallet-card {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .wallet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .logoutbtn{background-color:red;}
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="gradient-bg text-white p-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-chart-line text-2xl"></i>
                    <h1 class="text-2xl font-bold">WaveUnits P2P Exchange</h1>
                </div>
                <?php if (!$isLoggedIn): ?>
                <div id="loginSection">
                    <button onclick="showLoginForm()" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium">Login</button>
                    <button onclick="showRegisterForm()" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium ml-2">Register</button>
                </div>
                <?php else: ?>
                <div id="userSection" class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-xl cursor-pointer"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="<?php echo htmlspecialchars($user['profile_image'] ? 'uploads/'.$user['profile_image'] : 'https://randomuser.me/api/portraits/men/32.jpg'); ?>" alt="Profile" class="w-8 h-8 rounded-full border-2 border-white">
                        <span class="font-medium"><?php echo htmlspecialchars($user['first_name']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Login Modal -->
        <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Login to WaveUnits</h2>
                    <button onclick="hideLoginForm()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="loginForm" action="login.php" method="POST">
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">Login</button>
                </form>
                <p class="mt-4 text-center text-sm text-gray-600">Don't have an account? <a href="#" onclick="showRegisterForm()" class="text-purple-600">Register</a></p>
            </div>
        </div>

        <!-- Register Modal -->
        <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Register for WaveUnits</h2>
                    <button onclick="hideRegisterForm()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="registerForm" action="register.php" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" id="firstName" name="first_name" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" id="lastName" name="last_name" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="regEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="regEmail" name="email" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g. 254712345678">
                    </div>
                    <div class="mb-4">
                        <label for="regPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="regPassword" name="password" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="mb-4">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="mb-4">
                        <label for="profileImage" class="block text-sm font-medium text-gray-700 mb-1">Profile Image (Optional)</label>
                        <input type="file" id="profileImage" name="profile_image" accept="image/*" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">Register</button>
                </form>
                <p class="mt-4 text-center text-sm text-gray-600">Already have an account? <a href="#" onclick="showLoginForm()" class="text-purple-600">Login</a></p>
            </div>
        </div>

        <!-- Buy Shares Modal -->
        <div id="buyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Buy WaveUnits Shares</h2>
                    <button onclick="hideBuyForm()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <label for="buyAmount" class="block text-sm font-medium text-gray-700 mb-1">Number of Shares</label>
                    <input type="number" id="buyAmount" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Enter number of shares">
                </div>
                <div class="mb-6">
                    <p class="text-sm text-gray-600">Price per share: <span class="font-bold">1 KSH</span></p>
                    <p class="text-sm text-gray-600 mt-1">Total cost: <span id="buyTotalCost" class="font-bold">0 KSH</span></p>
                </div>
                <button onclick="confirmBuy()" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">Confirm Purchase</button>
            </div>
        </div>

        <!-- Sell Shares Modal -->
        <div id="sellModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Sell WaveUnits Shares</h2>
                    <button onclick="hideSellForm()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <label for="sellAmount" class="block text-sm font-medium text-gray-700 mb-1">Number of Shares</label>
                    <input type="number" id="sellAmount" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Enter number of shares">
                </div>
                <div class="mb-6">
                    <p class="text-sm text-gray-600">Price per share: <span class="font-bold">1 KSH</span></p>
                    <p class="text-sm text-gray-600 mt-1">Total proceeds: <span id="sellTotalProceeds" class="font-bold">0 KSH</span></p>
                </div>
                <button onclick="confirmSell()" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition">Confirm Sale</button>
            </div>
        </div>

        <!-- Transaction Confirmation Modal -->
        <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <h3 id="confirmationTitle" class="text-lg font-medium text-gray-900 mt-3">Transaction Successful</h3>
                    <div class="mt-2">
                        <p id="confirmationMessage" class="text-sm text-gray-500">Your transaction has been completed successfully.</p>
                    </div>
                    <div class="mt-6">
                        <button onclick="hideConfirmationModal()" type="button" class="w-full bg-purple-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto p-4 md:p-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Left Sidebar - Trading Section -->
                <div class="w-full md:w-1/3 space-y-6">
                    <!-- Trading Info -->
                    <div class="bg-white rounded-xl p-6 shadow-md">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">WaveUnits Trading</h2>
                        <div class="mb-4">
                            <p class="text-gray-600">Fixed Price: <span class="font-bold">1 KSH per share</span></p>
                            <p class="text-green-600 mt-2">Earn 10% monthly interest on held shares!</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="showBuyForm()" class="flex-grow bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition">
                                Buy Shares
                            </button>
                            <button onclick="showSellForm()" class="flex-grow bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition">
                                Sell Shares
                            </button>
                        </div>
                    </div>
                    <!-- Wallet Card -->
                    <div class="wallet-card bg-white rounded-xl p-6 shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-800">My Wallet</h2>
                            <i class="fas fa-wallet text-2xl text-purple-600"></i>
                        </div>
                        <div class="mb-4">
                            <p class="text-gray-500 text-sm">Available Balance</p>
                            <p class="text-3xl font-bold text-gray-800">KSh <span id="walletBalance"><?php echo $isLoggedIn ? number_format($user['balance'], 2) : '0.00'; ?></span></p>
                        </div>
                        <div class="mb-4">
                            <p class="text-gray-500 text-sm">WaveUnits Shares</p>
                            <p class="text-2xl font-bold text-gray-800"><span id="shareBalance"><?php echo $isLoggedIn ? $user['shares'] : '0'; ?></span> shares</p>
                            <p class="text-green-600 text-sm mt-1">Earning 10% monthly interest</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="openAddMoneyModal()" class="flex-grow bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Deposit via M-Pesa</span>
                            </button>
                            <button onclick="showWithdrawModal()" class="flex-grow bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Withdraw</span>
                            </button>
                        </div>
                    </div>

                    <!-- P2P Transactions -->
                    <div class="bg-white rounded-xl p-6 shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-800">P2P Transactions</h2>
                        </div>
                        <div class="space-y-4">
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-sm text-gray-700">
                                    <i class="fas fa-info-circle text-yellow-500 mr-1"></i>
                                    Sellers receive WhatsApp notifications for each buyer interest. Funds and shares are automatically transferred upon transaction completion.
                                </p>
                            
                            </div>
                            <button class="logoutbtn">log out</button>
                            <div id="transactionHistory">
                                <!-- Transactions will be loaded via AJAX -->
                                <?php if ($isLoggedIn): ?>
                                <script>
                                    loadTransactionHistory();
                                </script>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Marketplace Section -->
                <div class="w-full md:w-2/3 bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Marketplace Tabs -->
                    <div class="flex border-b">
                        <button onclick="switchTab('buyOffers')" class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-purple-600 text-purple-600">
                            <i class="fas fa-shopping-cart mr-2"></i>Buy Offers
                        </button>
                        <button onclick="switchTab('sellOffers')" class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500">
                            <i class="fas fa-coins mr-2"></i>Sell Offers
                        </button>
                        <button onclick="switchTab('createOffer')" class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500">
                            <i class="fas fa-plus-circle mr-2"></i>Create Offer
                        </button>
                    </div>

                    <!-- Buy Offers Tab -->
                    <div id="buyOffers" class="tab-content active p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shares</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price/Share</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="buyOffersTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Buy offers will be loaded via AJAX -->
                                    <?php if ($isLoggedIn): ?>
                                    <script>
                                        loadBuyOffers();
                                    </script>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sell Offers Tab -->
                    <div id="sellOffers" class="tab-content p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buyer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shares</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price/Share</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="sellOffersTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Sell offers will be loaded via AJAX -->
                                    <?php if ($isLoggedIn): ?>
                                    <script>
                                        loadSellOffers();
                                    </script>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Create Offer Tab -->
                    <div id="createOffer" class="tab-content p-4">
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Offer</h3>
                            <form id="offerForm" onsubmit="return handleCreateOffer(event)">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Type</label>
                                    <div class="mt-1">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="offerType" value="buy" checked class="h-4 w-4 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2">Buy Offer</span>
                                        </label>
                                        <label class="inline-flex items-center ml-6">
                                            <input type="radio" name="offerType" value="sell" class="h-4 w-4 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2">Sell Offer</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="shareAmount" class="block text-sm font-medium text-gray-700 mb-1">Number of Shares</label>
                                    <input type="number" id="shareAmount" class="w-full p-2 border rounded-md focus:ring-purple-500 focus:border-purple-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Price per Share</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">KSh</span>
                                        </div>
                                        <input type="text" value="1.00" readonly class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md bg-gray-100">
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full bg-purple-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Create Offer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Chat Tabs -->
                    <div class="flex border-b">
                        <button onclick="switchTab('contacts')" class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-purple-600 text-purple-600">
                            <i class="fas fa-user-friends mr-2"></i>Contacts
                        </button>
                        <button onclick="switchTab('chats')" class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500">
                            <i class="fas fa-comments mr-2"></i>Chats
                        </button>
                    </div>

                    <!-- Contacts Tab Content -->
                    <div id="contacts" class="tab-content active p-4">
                        <div class="relative mb-4">
                            <input type="text" placeholder="Search contacts..." class="w-full p-3 pl-10 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <div class="space-y-3" id="contactsList">
                            <!-- Contacts will be loaded via AJAX -->
                            <?php if ($isLoggedIn): ?>
                            <script>
                                loadContacts();
                            </script>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Chats Tab Content -->
                    <div id="chats" class="tab-content p-4">
                        <div class="relative mb-4">
                            <input type="text" placeholder="Search chats..." class="w-full p-3 pl-10 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <div class="space-y-3" id="chatsList">
                            <!-- Chats will be loaded via AJAX -->
                            <?php if ($isLoggedIn): ?>
                            <script>
                                loadChatList();
                            </script>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Active Chat Area (hidden by default) -->
                    <div id="activeChat" class="hidden flex flex-col h-96">
                        <!-- Chat Header -->
                        <div class="flex items-center p-4 border-b">
                            <button onclick="closeChat()" class="mr-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <img id="chatUserImage" src="https://randomuser.me/api/portraits/women/44.jpg" alt="User" class="w-10 h-10 rounded-full">
                            <div class="ml-3">
                                <h3 id="chatUserName" class="font-medium">Sarah Johnson</h3>
                                <p class="text-xs text-gray-500">Online</p>
                            </div>
                            <div class="ml-auto flex space-x-3">
                                <button class="text-gray-500 hover:text-purple-600">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="text-gray-500 hover:text-purple-600">
                                    <i class="fas fa-video"></i>
                                </button>
                                <button class="text-gray-500 hover:text-purple-600">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div id="chatMessages" class="flex-grow p-4 overflow-y-auto space-y-3">
                            <!-- Messages will be loaded when chat is opened -->
                        </div>

                        <!-- Chat Input -->
                        <div class="p-3 border-t bg-gray-50">
                            <div class="flex items-center">
                                <button class="p-2 text-gray-500 hover:text-purple-600">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <input type="text" id="messageInput" placeholder="Type a message..." class="flex-grow p-2 mx-2 rounded-full border focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <button onclick="sendMessage()" class="p-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white p-4 text-center">
            <p>&copy; 2025 Waveunits. All rights reserved.</p>
        </footer>
    </div>

    <!-- Add Money Modal -->
    <div id="addMoneyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Add Money to Wallet</h2>
                <button onclick="closeAddMoneyModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-500">KSh</span>
                    <input type="number" id="amount" placeholder="0.00" class="w-full pl-8 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <div class="space-y-2">
                    <div class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="paymentMethod" id="mpesa" class="h-4 w-4 text-purple-600 focus:ring-purple-500" checked>
                        <label for="mpesa" class="ml-3 block text-sm font-medium text-gray-700">
                            <div class="flex items-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" alt="M-Pesa" class="h-6 ml-2">
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <button onclick="initiateMpesaDeposit()" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">Add Money</button>
        </div>
    </div>

    <!-- Withdraw Money Modal -->
    <div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Withdraw Money</h2>
                <button onclick="hideWithdrawModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <label for="withdrawAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount (KSh)</label>
                <input type="number" id="withdrawAmount" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Enter amount to withdraw">
            </div>
            <div class="mb-4">
                <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Phone Number</label>
                <input type="tel" id="phoneNumber" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g. 254712345678" value="<?php echo $isLoggedIn ? htmlspecialchars($user['phone']) : ''; ?>">
            </div>
            <button onclick="initiateMpesaWithdrawal()" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">Withdraw</button>
        </div>
    </div>

    <script>
        // User state
        let isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        let walletBalance = <?php echo $isLoggedIn ? $user['balance'] : '0'; ?>;
        let shareBalance = <?php echo $isLoggedIn ? $user['shares'] : '0'; ?>;
        let currentChat = null;
        let currentChatUserId = null;
        let offers = [];
        let transactions = [];

        // DOM elements
        const loginSection = document.getElementById('loginSection');
        const userSection = document.getElementById('userSection');
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const buyModal = document.getElementById('buyModal');
        const sellModal = document.getElementById('sellModal');
        const confirmationModal = document.getElementById('confirmationModal');
        const buyAmountInput = document.getElementById('buyAmount');
        const sellAmountInput = document.getElementById('sellAmount');
        const buyTotalCost = document.getElementById('buyTotalCost');
        const sellTotalProceeds = document.getElementById('sellTotalProceeds');
        const walletBalanceDisplay = document.getElementById('walletBalance');
        const shareBalanceDisplay = document.getElementById('shareBalance');
        const buyOffersTable = document.getElementById('buyOffersTable');
        const sellOffersTable = document.getElementById('sellOffersTable');
        const transactionHistory = document.getElementById('transactionHistory');
        const offerForm = document.getElementById('offerForm');
        const addMoneyModal = document.getElementById('addMoneyModal');
        const withdrawModal = document.getElementById('withdrawModal');

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            updateWalletDisplay();
            
            // Event listeners
            buyAmountInput?.addEventListener('input', updateBuyTotal);
            sellAmountInput?.addEventListener('input', updateSellTotal);
            
            if (isLoggedIn) {
                loadTransactionHistory();
                loadBuyOffers();
                loadSellOffers();
                loadContacts();
                loadChatList();
            }
        });

        // Registration functionality
        function showRegisterForm() {
            hideLoginForm();
            registerModal.classList.remove('hidden');
        }

        function hideRegisterForm() {
            registerModal.classList.add('hidden');
        }

        // Login functionality
        function showLoginForm() {
            hideRegisterForm();
            loginModal.classList.remove('hidden');
        }

        function hideLoginForm() {
            loginModal.classList.add('hidden');
        }

        // Buy functionality
        function showBuyForm() {
            if (!isLoggedIn) {
                showLoginForm();
                return;
            }
            buyModal.classList.remove('hidden');
        }

        function hideBuyForm() {
            buyModal.classList.add('hidden');
        }

        function updateBuyTotal() {
            const amount = parseInt(buyAmountInput.value) || 0;
            buyTotalCost.textContent = `${amount} KSH`;
        }

        function confirmBuy() {
            const amount = parseInt(buyAmountInput.value) || 0;
            
            if (amount <= 0) {
                alert('Please enter a valid number of shares to buy');
                return;
            }
            
            if (amount > walletBalance) {
                showConfirmation('Insufficient Funds', 'You don\'t have enough money in your wallet to complete this purchase.');
                return;
            }
            
            // Process purchase via API
            fetch('api/transactions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'buy',
                    amount: amount,
                    shares: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    walletBalance = data.new_balance;
                    shareBalance = data.new_shares;
                    updateWalletDisplay();
                    loadTransactionHistory();
                    hideBuyForm();
                    showConfirmation('Purchase Successful', `You have successfully purchased ${amount} WaveUnits shares for ${amount} KSH.`);
                } else {
                    alert(data.message || 'Error processing purchase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing purchase');
            });
        }

        // Sell functionality
        function showSellForm() {
            if (!isLoggedIn) {
                showLoginForm();
                return;
            }
            sellModal.classList.remove('hidden');
        }

        function hideSellForm() {
            sellModal.classList.add('hidden');
        }

        function updateSellTotal() {
            const amount = parseInt(sellAmountInput.value) || 0;
            sellTotalProceeds.textContent = `${amount} KSH`;
        }

        function confirmSell() {
            const amount = parseInt(sellAmountInput.value) || 0;
            
            if (amount <= 0) {
                alert('Please enter a valid number of shares to sell');
                return;
            }
            
            if (amount > shareBalance) {
                showConfirmation('Insufficient Shares', 'You don\'t have enough shares to complete this sale.');
                return;
            }
            
            // Process sale via API
            fetch('api/transactions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'sell',
                    amount: amount,
                    shares: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    walletBalance = data.new_balance;
                    shareBalance = data.new_shares;
                    updateWalletDisplay();
                    loadTransactionHistory();
                    hideSellForm();
                    showConfirmation('Sale Successful', `You have successfully sold ${amount} WaveUnits shares for ${amount} KSH.`);
                } else {
                    alert(data.message || 'Error processing sale');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing sale');
            });
        }

        // Offer functionality
        function loadBuyOffers() {
            fetch('api/offers.php?type=sell')
                .then(response => response.json())
                .then(data => {
                    offers = data;
                    renderOffers();
                })
                .catch(error => console.error('Error loading buy offers:', error));
        }

        function loadSellOffers() {
            fetch('api/offers.php?type=buy')
                .then(response => response.json())
                .then(data => {
                    offers = data;
                    renderOffers();
                })
                .catch(error => console.error('Error loading sell offers:', error));
        }

        function renderOffers() {
            // Clear existing offers
            buyOffersTable.innerHTML = '';
            sellOffersTable.innerHTML = '';
            
            // Filter and render buy offers (user wants to sell)
            const buyOffers = offers.filter(offer => offer.type === 'sell');
            buyOffers.forEach(offer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <img src="${offer.profile_image ? 'uploads/'+offer.profile_image : 'https://randomuser.me/api/portraits/men/32.jpg'}" alt="" class="w-8 h-8 rounded-full">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${offer.first_name} ${offer.last_name}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${offer.shares}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${offer.price} KSH</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="buyOffer(${offer.id})" class="text-green-600 hover:text-green-900">Buy</button>
                    </td>
                `;
                buyOffersTable.appendChild(row);
            });
            
            // Filter and render sell offers (user wants to buy)
            const sellOffers = offers.filter(offer => offer.type === 'buy');
            sellOffers.forEach(offer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <img src="${offer.profile_image ? 'uploads/'+offer.profile_image : 'https://randomuser.me/api/portraits/men/32.jpg'}" alt="" class="w-8 h-8 rounded-full">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${offer.first_name} ${offer.last_name}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${offer.shares}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${offer.price} KSH</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="sellOffer(${offer.id})" class="text-red-600 hover:text-red-900">Sell</button>
                    </td>
                `;
                sellOffersTable.appendChild(row);
            });
        }

        function buyOffer(offerId) {
            if (!isLoggedIn) {
                showLoginForm();
                return;
            }
            
            const offer = offers.find(o => o.id === offerId);
            if (!offer) return;
            
            // For demo purposes, we'll just show the buy form with the offer details
            showBuyForm();
            buyAmountInput.value = offer.shares;
            updateBuyTotal();
            
            // In a real app, this would initiate a P2P transaction with the seller
        }

        function sellOffer(offerId) {
            if (!isLoggedIn) {
                showLoginForm();
                return;
            }
            
            const offer = offers.find(o => o.id === offerId);
            if (!offer) return;
            
            // For demo purposes, we'll just show the sell form with the offer details
            showSellForm();
            sellAmountInput.value = offer.shares;
            updateSellTotal();
            
            // In a real app, this would initiate a P2P transaction with the buyer
        }

        function handleCreateOffer(e) {
            e.preventDefault();
            
            if (!isLoggedIn) {
                showLoginForm();
                return false;
            }
            
            const offerType = document.querySelector('input[name="offerType"]:checked').value;
            const shareAmount = parseInt(document.getElementById('shareAmount').value) || 0;
            
            if (shareAmount <= 0) {
                alert('Please enter a valid number of shares');
                return false;
            }
            
            if (offerType === 'sell' && shareAmount > shareBalance) {
                showConfirmation('Insufficient Shares', 'You don\'t have enough shares to create this sell offer.');
                return false;
            }
            
            // Create new offer via API
            fetch('api/offers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    type: offerType,
                    shares: shareAmount,
                    price: 1.00
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (offerType === 'buy') {
                        loadSellOffers();
                    } else {
                        loadBuyOffers();
                    }
                    const action = offerType === 'buy' ? 'Buy' : 'Sell';
                    showConfirmation('Offer Created', `Your ${action} offer for ${shareAmount} shares has been created.`);
                    offerForm.reset();
                } else {
                    alert(data.message || 'Error creating offer');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating offer');
            });
            
            return false;
        }

        // Wallet functionality
        function updateWalletDisplay() {
            walletBalanceDisplay.textContent = walletBalance.toFixed(2);
            shareBalanceDisplay.textContent = shareBalance;
        }

        function loadTransactionHistory() {
            fetch('api/transactions.php')
                .then(response => response.json())
                .then(data => {
                    transactions = data;
                    renderTransactionHistory();
                })
                .catch(error => console.error('Error loading transaction history:', error));
        }

        function renderTransactionHistory() {
            transactionHistory.innerHTML = '';
            
            transactions.forEach(tx => {
                const txElement = document.createElement('div');
                txElement.className = 'flex items-center justify-between';
                
                if (tx.type === 'buy') {
                    txElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-coins text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Bought ${tx.shares} shares</p>
                                <p class="text-gray-500 text-sm">${tx.created_at}</p>
                            </div>
                        </div>
                        <p class="font-bold text-green-500">-KSh ${tx.amount}</p>
                    `;
                } else if (tx.type === 'sell') {
                    txElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-100 p-2 rounded-full">
                                <i class="fas fa-hand-holding-usd text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Sold ${tx.shares} shares</p>
                                <p class="text-gray-500 text-sm">${tx.created_at}</p>
                            </div>
                        </div>
                        <p class="font-bold text-red-500">+KSh ${tx.amount}</p>
                    `;
                } else if (tx.type === 'deposit') {
                    txElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-money-bill-wave text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Deposit via M-Pesa</p>
                                <p class="text-gray-500 text-sm">${tx.created_at}</p>
                            </div>
                        </div>
                        <p class="font-bold text-blue-500">+KSh ${tx.amount}</p>
                    `;
                } else if (tx.type === 'withdrawal') {
                    txElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="bg-purple-100 p-2 rounded-full">
                                <i class="fas fa-money-bill-alt text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Withdrawal to M-Pesa</p>
                                <p class="text-gray-500 text-sm">${tx.created_at}</p>
                            </div>
                        </div>
                        <p class="font-bold text-purple-500">-KSh ${tx.amount}</p>
                    `;
                }
                
                transactionHistory.appendChild(txElement);
            });
        }

        function openAddMoneyModal() {
            if (!isLoggedIn) {
                showLoginForm();
                return;
            }
            addMoneyModal.classList.remove('hidden');
        }

        function closeAddMoneyModal() {
            addMoneyModal.classList.add('hidden');
        }

        function showWithdrawModal() {
            if (!isLoggedIn) {
                showLoginForm();
                return;
            }
            withdrawModal.classList.remove('hidden');
        }

        function hideWithdrawModal() {
            withdrawModal.classList.add('hidden');
        }

        function initiateMpesaDeposit() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            
            if (amount <= 0) {
                alert('Please enter a valid amount');
                return;
            }
            
            // Initiate M-Pesa deposit via API
            fetch('api/mpesa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'deposit',
                    amount: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddMoneyModal();
                    showConfirmation('Deposit Initiated', `An M-Pesa payment request has been sent to your phone. Please complete the payment to add ${amount.toFixed(2)} KSH to your wallet.`);
                    // Poll for payment completion
                    checkPaymentStatus(data.transaction_id);
                } else {
                    alert(data.message || 'Error initiating deposit');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error initiating deposit');
            });
        }

        function checkPaymentStatus(transactionId) {
            let attempts = 0;
            const maxAttempts = 10;
            const interval = 3000; // 3 seconds
            
            const checkInterval = setInterval(() => {
                attempts++;
                
                fetch(`api/mpesa.php?transaction_id=${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'completed') {
                            clearInterval(checkInterval);
                            walletBalance = parseFloat(data.new_balance);
                            updateWalletDisplay();
                            loadTransactionHistory();
                            showConfirmation('Deposit Successful', `You have successfully added ${data.amount} KSH to your wallet.`);
                        } else if (attempts >= maxAttempts) {
                            clearInterval(checkInterval);
                            showConfirmation('Payment Pending', 'The payment is still pending. Please check your M-Pesa transactions and refresh your balance later.');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking payment status:', error);
                        if (attempts >= maxAttempts) {
                            clearInterval(checkInterval);
                        }
                    });
            }, interval);
        }

        function initiateMpesaWithdrawal() {
            const amount = parseFloat(document.getElementById('withdrawAmount').value) || 0;
            const phoneNumber = document.getElementById('phoneNumber').value;
            
            if (amount <= 0) {
                alert('Please enter a valid amount');
                return;
            }
            
            if (!phoneNumber) {
                alert('Please enter your M-Pesa phone number');
                return;
            }
            
            if (amount > walletBalance) {
                showConfirmation('Insufficient Funds', 'You don\'t have enough money in your wallet for this withdrawal.');
                return;
            }
            
            // Initiate M-Pesa withdrawal via API
            fetch('api/mpesa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'withdraw',
                    amount: amount,
                    phone: phoneNumber
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideWithdrawModal();
                    showConfirmation('Withdrawal Initiated', `${amount.toFixed(2)} KSH is being sent to M-Pesa number ${phoneNumber}. Please check your phone for confirmation.`);
                    // Update balance after successful withdrawal
                    walletBalance = parseFloat(data.new_balance);
                    updateWalletDisplay();
                    loadTransactionHistory();
                } else {
                    alert(data.message || 'Error initiating withdrawal');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error initiating withdrawal');
            });
        }

        // Chat functionality
        function loadContacts() {
            fetch('api/chat.php?action=contacts')
                .then(response => response.json())
                .then(data => {
                    const contactsList = document.getElementById('contactsList');
                    contactsList.innerHTML = '';
                    
                    data.forEach(contact => {
                        const contactElement = document.createElement('div');
                        contactElement.className = 'flex items-center p-3 hover:bg-gray-100 rounded-lg cursor-pointer transition';
                        contactElement.onclick = () => startChat(contact.id, contact.first_name + ' ' + contact.last_name, contact.profile_image);
                        
                        contactElement.innerHTML = `
                            <img src="${contact.profile_image ? 'uploads/'+contact.profile_image : 'https://randomuser.me/api/portraits/men/32.jpg'}" alt="${contact.first_name}" class="w-12 h-12 rounded-full border-2 border-purple-200">
                            <div class="ml-3 flex-grow">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-medium">${contact.first_name} ${contact.last_name}</h3>
                                    <span class="text-xs text-gray-500">${contact.online ? 'Online' : 'Offline'}</span>
                                </div>
                                <p class="text-sm text-gray-500">Last seen ${contact.last_seen}</p>
                            </div>
                        `;
                        
                        contactsList.appendChild(contactElement);
                    });
                })
                .catch(error => console.error('Error loading contacts:', error));
        }

        function loadChatList() {
            fetch('api/chat.php?action=chats')
                .then(response => response.json())
                .then(data => {
                    const chatsList = document.getElementById('chatsList');
                    chatsList.innerHTML = '';
                    
                    data.forEach(chat => {
                        const chatElement = document.createElement('div');
                        chatElement.className = 'flex items-center p-3 hover:bg-gray-100 rounded-lg cursor-pointer transition';
                        chatElement.onclick = () => openChat(chat.user_id, chat.first_name + ' ' + chat.last_name, chat.profile_image);
                        
                        chatElement.innerHTML = `
                            <img src="${chat.profile_image ? 'uploads/'+chat.profile_image : 'https://randomuser.me/api/portraits/men/32.jpg'}" alt="${chat.first_name}" class="w-12 h-12 rounded-full border-2 border-purple-200">
                            <div class="ml-3 flex-grow">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-medium">${chat.first_name} ${chat.last_name}</h3>
                                    <span class="text-xs text-gray-500">${chat.last_message_time}</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate">${chat.last_message}</p>
                            </div>
                            ${chat.unread_count > 0 ? `<div class="ml-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">${chat.unread_count}</div>` : ''}
                        `;
                        
                        chatsList.appendChild(chatElement);
                    });
                })
                .catch(error => console.error('Error loading chat list:', error));
        }

        function startChat(userId, userName, userImage) {
            currentChatUserId = userId;
            currentChat = userName;
            document.getElementById('chatUserName').textContent = userName;
            document.getElementById('chatUserImage').src = userImage || 'https://randomuser.me/api/portraits/men/32.jpg';
            document.getElementById('activeChat').classList.remove('hidden');
            document.getElementById('contacts').classList.add('hidden');
            document.getElementById('chats').classList.add('hidden');
            
            // Load chat messages
            loadChatMessages(userId);
        }

        function openChat(userId, userName, userImage) {
            currentChatUserId = userId;
            currentChat = userName;
            document.getElementById('chatUserName').textContent = userName;
            document.getElementById('chatUserImage').src = userImage || 'https://randomuser.me/api/portraits/men/32.jpg';
            document.getElementById('activeChat').classList.remove('hidden');
            document.getElementById('contacts').classList.add('hidden');
            document.getElementById('chats').classList.add('hidden');
            
            // Load chat messages
            loadChatMessages(userId);
        }

        function closeChat() {
            document.getElementById('activeChat').classList.add('hidden');
            document.getElementById('contacts').classList.remove('hidden');
            currentChat = null;
            currentChatUserId = null;
        }

        function loadChatMessages(userId) {
            fetch(`api/chat.php?action=messages&user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    const chatMessages = document.getElementById('chatMessages');
                    chatMessages.innerHTML = '';
                    
                    data.forEach(msg => {
                        const messageElement = document.createElement('div');
                        messageElement.className = `flex ${msg.sent ? 'justify-end' : 'justify-start'}`;
                        
                        messageElement.innerHTML = `
                            <div class="chat-bubble ${msg.sent ? 'sent' : 'received'} p-3 max-w-xs">
                                <p class="text-sm">${msg.message}</p>
                                <p class="text-xs opacity-70 mt-1 text-right">${msg.created_at}</p>
                            </div>
                        `;
                        
                        chatMessages.appendChild(messageElement);
                    });
                    
                    // Scroll to bottom
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                })
                .catch(error => console.error('Error loading chat messages:', error));
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message || !currentChatUserId) return;
            
            const chatMessages = document.getElementById('chatMessages');
            const now = new Date();
            const timeString = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
            
            // Add sent message immediately for better UX
            const sentMessage = document.createElement('div');
            sentMessage.className = 'flex justify-end';
            sentMessage.innerHTML = `
                <div class="chat-bubble sent p-3 max-w-xs">
                    <p class="text-sm">${message}</p>
                    <p class="text-xs opacity-70 mt-1 text-right">${timeString}</p>
                </div>
            `;
            chatMessages.appendChild(sentMessage);
            
            // Clear input
            messageInput.value = '';
            
            // Send message to server
            fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send',
                    receiver_id: currentChatUserId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error sending message:', data.message);
                }
                // Reload chat list to update last message
                loadChatList();
            })
            .catch(error => {
                console.error('Error sending message:', error);
            });
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Utility functions
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Deactivate all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-purple-600', 'text-purple-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Activate selected tab
            document.getElementById(tabId).classList.add('active');
            
            // Activate corresponding button
            const buttons = document.querySelectorAll('.tab-button');
            for (let i = 0; i < buttons.length; i++) {
                if (buttons[i].getAttribute('onclick').includes(tabId)) {
                    buttons[i].classList.remove('border-transparent', 'text-gray-500');
                    buttons[i].classList.add('border-purple-600', 'text-purple-600');
                    break;
                }
            }
        }

        function showConfirmation(title, message) {
            document.getElementById('confirmationTitle').textContent = title;
            document.getElementById('confirmationMessage').textContent = message;
            confirmationModal.classList.remove('hidden');
        }

        function hideConfirmationModal() {
            confirmationModal.classList.add('hidden');
        }
    </script>
</body>
</html>