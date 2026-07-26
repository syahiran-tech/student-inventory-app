package carrentalsystem;

import java.util.ArrayList;
import java.util.Scanner;
import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * Main - Terminal-based Car Rental System.
 * Features: Input validation, booking lifecycle, payment, reviews.
 * 
 * ACCOUNT HIERARCHY:
 *   Manager -> can create Staff accounts & manage everything
 *   Staff   -> can manage fleet, bookings, customers, returns
 *   Customer -> can register themselves, book cars, write reviews
 * 
 * First-time default manager: admin / admin123
 */
public class Main {
    private static Scanner sc = new Scanner(System.in);

    // ==================== INPUT HELPERS ====================
    private static String readLine(String prompt) {
        System.out.print(prompt);
        return sc.nextLine().trim();
    }

    private static String readNonEmpty(String prompt) {
        while (true) {
            String val = readLine(prompt);
            if (!val.isEmpty()) return val;
            System.out.println("  [!] This field cannot be empty. Try again.");
        }
    }

    private static int readInt(String prompt) {
        while (true) {
            try { return Integer.parseInt(readLine(prompt)); }
            catch (NumberFormatException e) { System.out.println("  [!] Please enter a valid whole number."); }
        }
    }

    private static int readInt(String prompt, int min, int max) {
        while (true) {
            int val = readInt(prompt);
            if (val >= min && val <= max) return val;
            System.out.println("  [!] Enter a number between " + min + " and " + max + ".");
        }
    }

    private static double readDouble(String prompt) {
        while (true) {
            try {
                double d = Double.parseDouble(readLine(prompt));
                if (d < 0) { System.out.println("  [!] Cannot be negative."); continue; }
                return d;
            } catch (NumberFormatException e) { System.out.println("  [!] Please enter a valid number (e.g. 150.00)."); }
        }
    }

    private static String readPhone(String prompt) {
        while (true) {
            String val = readLine(prompt);
            if (val.matches("[0-9\\-\\+]{7,15}")) return val;
            System.out.println("  [!] Invalid phone. Use digits only (7-15 chars).");
        }
    }

    private static String readEmail(String prompt) {
        while (true) {
            String val = readLine(prompt);
            if (val.matches("[a-zA-Z0-9._%+\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}")) return val;
            System.out.println("  [!] Invalid email. Example: user@email.com");
        }
    }

    private static String readDate(String prompt) {
        while (true) {
            String val = readLine(prompt);
            if (val.matches("\\d{2}/\\d{2}/\\d{4}")) {
                try { SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy"); sdf.setLenient(false); sdf.parse(val); return val; }
                catch (Exception e) { System.out.println("  [!] Invalid date. Use DD/MM/YYYY."); }
            } else { System.out.println("  [!] Format must be DD/MM/YYYY (e.g. 15/04/2026)."); }
        }
    }

    private static String readChoice(String prompt, String[] valid) {
        while (true) {
            String val = readLine(prompt);
            for (String v : valid) if (v.equalsIgnoreCase(val)) return v;
            StringBuilder sb = new StringBuilder("  [!] Choose from: ");
            for (int i = 0; i < valid.length; i++) { if (i > 0) sb.append(", "); sb.append(valid[i]); }
            System.out.println(sb.toString());
        }
    }

    private static boolean readYesNo(String prompt) {
        return readChoice(prompt, new String[]{"y", "n"}).equalsIgnoreCase("y");
    }

    private static String today() { return new SimpleDateFormat("dd/MM/yyyy").format(new Date()); }

    // ==================== MAIN ====================
    public static void main(String[] args) {
        DataManager.initDataDirectory();
        DataManager.loadBalance();
        homeMenu();
    }

    // ==================== HOME MENU ====================
    private static void homeMenu() {
        while (true) {
            System.out.println("\n============================================");
            System.out.println("           _______");
            System.out.println("          //  ||\\ \\");
            System.out.println("    _____//___||_\\ \\___");
            System.out.println("    )  _          _    \\");
            System.out.println("    |_/ \\________/ \\___|");
            System.out.println("   ___\\_/________\\_/______");
            System.out.println();
            System.out.println("       CAR RENTAL MANAGEMENT SYSTEM");
            System.out.println("============================================");
            System.out.println("  1. Login as Manager");
            System.out.println("     (Manages staff, fleet, reports, balance)");
            System.out.println();
            System.out.println("  2. Login as Staff");
            System.out.println("     (Manages bookings, returns, customers)");
            System.out.println("     * Staff accounts are created by Manager");
            System.out.println();
            System.out.println("  3. Login as Customer");
            System.out.println("     (Book cars, view bookings, write reviews)");
            System.out.println();
            System.out.println("  4. Register New Customer Account");
            System.out.println("     (Create your account to start booking)");
            System.out.println();
            System.out.println("  0. Exit");
            System.out.println("============================================");
            switch (readLine("  Enter choice (0-4): ")) {
                case "1": login("Manager"); break;
                case "2": login("Staff"); break;
                case "3": login("Customer"); break;
                case "4": registerCustomer(); break;
                case "0": DataManager.saveBalance(); System.out.println("\n  Thank you for using the system. Goodbye!"); return;
                default: System.out.println("  [!] Invalid choice. Please enter 0, 1, 2, 3, or 4.");
            }
        }
    }

    // ==================== LOGIN ====================
    private static void login(String role) {
        System.out.println("\n  +-----------------------+");
        System.out.println("  |   " + role + " Login" + spaces(13 - role.length()) + "|");
        System.out.println("  +-----------------------+");

        if (role.equals("Staff")) {
            ArrayList<Staff> staffList = DataManager.loadStaff();
            if (staffList.isEmpty()) {
                System.out.println("  [!] No staff accounts exist yet.");
                System.out.println("      Login as Manager first and create staff");
                System.out.println("      accounts via Staff Management > Add Staff.");
                return;
            }
        }

        String username = readNonEmpty("  Username: ");
        String password = readNonEmpty("  Password: ");

        switch (role) {
            case "Manager":
                for (Manager m : DataManager.loadManagers())
                    if (m.getUsername().equals(username) && m.getPassword().equals(password)) {
                        System.out.println("\n  Welcome back, " + m.getName() + "! (Manager)");
                        managerMenu(m); return;
                    }
                break;
            case "Staff":
                for (Staff s : DataManager.loadStaff())
                    if (s.getUsername().equals(username) && s.getPassword().equals(password)) {
                        System.out.println("\n  Welcome back, " + s.getName() + "! (Staff - " + s.getPosition() + ")");
                        staffMenu(s); return;
                    }
                break;
            case "Customer":
                for (Customer c : DataManager.loadCustomers())
                    if (c.getUsername().equals(username) && c.getPassword().equals(password)) {
                        System.out.println("\n  Welcome back, " + c.getName() + "!");
                        customerMenu(c); return;
                    }
                break;
        }
        System.out.println("  [!] Invalid username or password.");
        if (role.equals("Customer")) {
            System.out.println("      Don't have an account? Go back and choose option 4 to register.");
        }
    }

    private static String spaces(int n) { StringBuilder sb = new StringBuilder(); for (int i = 0; i < n; i++) sb.append(" "); return sb.toString(); }

    // ==================== REGISTER CUSTOMER ====================
    private static void registerCustomer() {
        System.out.println("\n  +--------------------------+");
        System.out.println("  |  Customer Registration   |");
        System.out.println("  +--------------------------+");
        System.out.println("  Fill in your details below to create an account.\n");

        String username = readNonEmpty("  Choose a username: ");
        for (Customer c : DataManager.loadCustomers()) {
            if (c.getUsername().equalsIgnoreCase(username)) {
                System.out.println("  [!] Username '" + username + "' is already taken. Try a different one.");
                return;
            }
        }

        String password = readNonEmpty("  Choose a password: ");
        String name     = readNonEmpty("  Full Name: ");
        String phone    = readPhone("  Phone Number: ");
        String email    = readEmail("  Email Address: ");

        System.out.println("\n  --- Driving License Details ---");
        String licNum  = readNonEmpty("  License Number: ");
        String licType = readNonEmpty("  License Type (eg, L, P): ");
        String licExp  = readDate("  License Expiry Date (DD/MM/YYYY): ");

        ArrayList<Customer> customers = DataManager.loadCustomers();
        String id = DataManager.generateId("CUS", customers.size());
        customers.add(new Customer(id, username, password, name, phone, email, new License(licNum, licType, licExp)));
        DataManager.saveCustomers(customers);

        System.out.println("\n  ================================");
        System.out.println("  Registration Successful!");
        System.out.println("  ================================");
        System.out.println("  Your Username : " + username);
        System.out.println("  Your ID       : " + id);
        System.out.println("  ================================");
        System.out.println("  You can now login with option 3.");
    }

    // ==================== MANAGER DASHBOARD ====================
    private static void managerMenu(Manager m) {
        while (true) {
            System.out.println("\n============================================");
            System.out.println("    MANAGER DASHBOARD - " + m.getName());
            System.out.println("============================================");
            System.out.println("  1. Staff Management    (Add/Edit/Delete Staff)");
            System.out.println("  2. Fleet Management    (Add/Edit/Delete Cars)");
            System.out.println("  3. Booking Management  (View/Complete/Cancel)");
            System.out.println("  4. Customer Management (View/Edit/Delete)");
            System.out.println("  5. Supplier Management (Add/Edit/Delete)");
            System.out.println("  6. Return & Logistics  (View Returns/Invoices)");
            System.out.println("  7. Reports & Analytics (Revenue/Stats)");
            System.out.println("  8. Company Balance     (Financial Summary)");
            System.out.println("  9. Change My Password");
            System.out.println("  0. Logout");
            System.out.println("============================================");
            switch (readLine("  Choice: ")) {
                case "1": staffCRUD(); break;
                case "2": fleetCRUD(); break;
                case "3": bookingManagement(); break;
                case "4": customerCRUD(); break;
                case "5": supplierCRUD(); break;
                case "6": returnMenu(); break;
                case "7": reportsMenu(); break;
                case "8": showBalance(); break;
                case "9": changePassword(m); break;
                case "0": System.out.println("  Logged out."); return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== STAFF DASHBOARD ====================
    private static void staffMenu(Staff s) {
        while (true) {
            System.out.println("\n============================================");
            System.out.println("    STAFF DASHBOARD - " + s.getName());
            System.out.println("    Position: " + s.getPosition());
            System.out.println("============================================");
            System.out.println("  1. Fleet Management    (View/Add/Edit Cars)");
            System.out.println("  2. Booking Management  (View/Complete/Cancel)");
            System.out.println("  3. Customer Management (View/Edit)");
            System.out.println("  4. Return & Logistics  (View Returns/Invoices)");
            System.out.println("  5. My Profile          (Edit Name/Phone/Password)");
            System.out.println("  0. Logout");
            System.out.println("============================================");
            switch (readLine("  Choice: ")) {
                case "1": fleetCRUD(); break;
                case "2": bookingManagement(); break;
                case "3": customerCRUD(); break;
                case "4": returnMenu(); break;
                case "5": editStaffProfile(s); break;
                case "0": System.out.println("  Logged out."); return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== CUSTOMER DASHBOARD ====================
    private static void customerMenu(Customer c) {
        while (true) {
            System.out.println("\n============================================");
            System.out.println("    CUSTOMER DASHBOARD - " + c.getName());
            System.out.println("============================================");
            System.out.println("  1. Browse & Book Cars");
            System.out.println("  2. My Bookings");
            System.out.println("  3. Write a Review (completed bookings only)");
            System.out.println("  4. My Profile");
            System.out.println("  0. Logout");
            System.out.println("============================================");
            switch (readLine("  Choice: ")) {
                case "1": customerBooking(c); break;
                case "2": viewMyBookings(c); break;
                case "3": writeReview(c); break;
                case "4": editCustomerProfile(c); break;
                case "0": System.out.println("  Logged out."); return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== CHANGE PASSWORD (Manager) ====================
    private static void changePassword(Manager m) {
        System.out.println("\n--- Change Password ---");
        String oldPw = readNonEmpty("  Current password: ");
        if (!oldPw.equals(m.getPassword())) { System.out.println("  [!] Incorrect current password."); return; }
        String newPw = readNonEmpty("  New password: ");
        String confirm = readNonEmpty("  Confirm new password: ");
        if (!newPw.equals(confirm)) { System.out.println("  [!] Passwords don't match."); return; }
        m.setPassword(newPw);
        ArrayList<Manager> ml = DataManager.loadManagers();
        for (Manager mg : ml) if (mg.getId().equals(m.getId())) { mg.setPassword(newPw); break; }
        DataManager.saveManagers(ml);
        System.out.println("  Password changed successfully!");
    }

    // ==================== STAFF CRUD ====================
    private static void staffCRUD() {
        while (true) {
            System.out.println("\n--- Staff Management ---");
            System.out.println("  1. View All Staff     2. Add Staff");
            System.out.println("  3. Modify Staff       4. Delete Staff");
            System.out.println("  5. Search Staff       0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    ArrayList<Staff> sl = DataManager.loadStaff();
                    if (sl.isEmpty()) { System.out.println("\n  No staff accounts yet. Use option 2 to add staff."); break; }
                    System.out.println("\n  --- Staff List ---");
                    for (Staff s : sl) System.out.println("  " + s);
                    System.out.println("  --- Total: " + sl.size() + " staff ---");
                    break;
                case "2":
                    System.out.println("\n  --- Add New Staff ---");
                    System.out.println("  The staff will use these credentials to login.\n");
                    String u = readNonEmpty("  Username: ");
                    for (Staff s : DataManager.loadStaff()) if (s.getUsername().equalsIgnoreCase(u)) { System.out.println("  [!] Username already exists."); u = null; break; }
                    if (u == null) break;
                    String pw = readNonEmpty("  Password: ");
                    String n = readNonEmpty("  Full Name: ");
                    String ph = readPhone("  Phone: ");
                    String em = readEmail("  Email: ");
                    String pos = readNonEmpty("  Position (e.g. Receptionist, Mechanic): ");
                    double sal = readDouble("  Monthly Salary (RM): ");
                    ArrayList<Staff> sList = DataManager.loadStaff();
                    String id = DataManager.generateId("STF", sList.size());
                    sList.add(new Staff(id, u, pw, n, ph, em, pos, sal));
                    DataManager.saveStaff(sList);
                    System.out.println("\n  Staff account created!");
                    System.out.println("  ID: " + id + " | Username: " + u);
                    System.out.println("  They can now login via Staff Login (option 2 on home screen).");
                    break;
                case "3":
                    String mid = readNonEmpty("  Enter Staff ID to modify: ");
                    ArrayList<Staff> msl = DataManager.loadStaff();
                    Staff mfound = null;
                    for (Staff s : msl) if (s.getId().equalsIgnoreCase(mid)) { mfound = s; break; }
                    if (mfound == null) { System.out.println("  [!] Staff not found."); break; }
                    System.out.println("  Current: " + mfound);
                    System.out.println("  (Press Enter to keep current value)\n");
                    String v = readLine("  Name [" + mfound.getName() + "]: "); if (!v.isEmpty()) mfound.setName(v);
                    v = readLine("  Phone [" + mfound.getPhone() + "]: ");
                    if (!v.isEmpty()) { if (v.matches("[0-9\\-\\+]{7,15}")) mfound.setPhone(v); else System.out.println("  [!] Invalid phone, kept old."); }
                    v = readLine("  Email [" + mfound.getEmail() + "]: ");
                    if (!v.isEmpty()) { if (v.contains("@") && v.contains(".")) mfound.setEmail(v); else System.out.println("  [!] Invalid email, kept old."); }
                    v = readLine("  Position [" + mfound.getPosition() + "]: "); if (!v.isEmpty()) mfound.setPosition(v);
                    v = readLine("  Salary [" + mfound.getSalary() + "]: ");
                    if (!v.isEmpty()) { try { mfound.setSalary(Double.parseDouble(v)); } catch (NumberFormatException e) { System.out.println("  [!] Invalid salary, kept old."); } }
                    DataManager.saveStaff(msl);
                    System.out.println("  Staff updated!");
                    break;
                case "4":
                    String did = readNonEmpty("  Enter Staff ID to delete: ");
                    ArrayList<Staff> dsl = DataManager.loadStaff();
                    boolean df = false;
                    for (int i = 0; i < dsl.size(); i++) {
                        if (dsl.get(i).getId().equalsIgnoreCase(did)) {
                            System.out.println("  Staff: " + dsl.get(i).getName() + " (" + dsl.get(i).getPosition() + ")");
                            if (!readYesNo("  Confirm delete? (y/n): ")) { System.out.println("  Cancelled."); break; }
                            dsl.remove(i); DataManager.saveStaff(dsl);
                            System.out.println("  Staff deleted."); df = true; break;
                        }
                    }
                    if (!df) System.out.println("  [!] Staff not found.");
                    break;
                case "5":
                    String kw = readNonEmpty("  Search keyword: ").toLowerCase();
                    boolean sf = false;
                    for (Staff s : DataManager.loadStaff())
                        if (s.getName().toLowerCase().contains(kw) || s.getId().toLowerCase().contains(kw) || s.getPosition().toLowerCase().contains(kw)) { System.out.println("  " + s); sf = true; }
                    if (!sf) System.out.println("  No results found.");
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== FLEET CRUD ====================
    private static void fleetCRUD() {
        while (true) {
            System.out.println("\n--- Fleet Management ---");
            System.out.println("  1. View All Cars      2. Add Car");
            System.out.println("  3. Modify Car         4. Delete Car");
            System.out.println("  5. Change Status      6. Update Mileage");
            System.out.println("  7. Filter by Category 0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    ArrayList<Vehicle> vl = DataManager.loadVehicles();
                    if (vl.isEmpty()) { System.out.println("  No vehicles."); break; }
                    System.out.println();
                    for (Vehicle ve : vl) System.out.println("  " + ve);
                    System.out.println("  --- Total: " + vl.size() + " vehicles ---");
                    break;
                case "2":
                    System.out.println("\n  --- Add New Vehicle ---");
                    String cat = readChoice("  Category (Economy/SUV/Luxury): ", new String[]{"Economy", "SUV", "Luxury"});
                    String brand = readNonEmpty("  Brand (e.g. Toyota, Perodua): ");
                    String model = readNonEmpty("  Model (e.g. Myvi, Fortuner): ");
                    int year = readInt("  Year (e.g. 2024): ", 1990, 2030);
                    String plate = readNonEmpty("  Plate Number (e.g. WA1234): ");
                    String trans = readChoice("  Transmission (Auto/Manual): ", new String[]{"Auto", "Manual"});
                    int seats = readInt("  Seating Capacity: ", 1, 20);
                    double rate = readDouble("  Daily Rate (RM): ");
                    boolean extra = readYesNo("  Has special feature? (y/n): ");
                    if (extra) {
                        switch (cat) { case "Economy": System.out.println("    -> AirCon included"); break; case "SUV": System.out.println("    -> 4WD enabled"); break; case "Luxury": System.out.println("    -> Chauffeur included"); break; }
                    }
                    ArrayList<Vehicle> avl = DataManager.loadVehicles();
                    String vid = DataManager.generateId("V", avl.size());
                    Vehicle nv;
                    switch (cat) {
                        case "SUV": nv = new SUVCar(vid, brand, model, year, plate, trans, seats, 0, "Available", rate, extra); break;
                        case "Luxury": nv = new LuxuryCar(vid, brand, model, year, plate, trans, seats, 0, "Available", rate, extra); break;
                        default: nv = new EconomyCar(vid, brand, model, year, plate, trans, seats, 0, "Available", rate, extra); break;
                    }
                    avl.add(nv); DataManager.saveVehicles(avl);
                    System.out.println("  Vehicle added! ID: " + vid + " | " + brand + " " + model + " [" + cat + "] RM" + String.format("%.2f", rate) + "/day");
                    break;
                case "3":
                    String mvid = readNonEmpty("  Vehicle ID to modify: ");
                    ArrayList<Vehicle> mvl = DataManager.loadVehicles();
                    Vehicle mfound = null;
                    for (Vehicle ve : mvl) if (ve.getVehicleId().equalsIgnoreCase(mvid)) { mfound = ve; break; }
                    if (mfound == null) { System.out.println("  [!] Vehicle not found."); break; }
                    System.out.println("  Current: " + mfound);
                    System.out.println("  (Press Enter to keep current value)\n");
                    String x = readLine("  Brand [" + mfound.getBrand() + "]: "); if (!x.isEmpty()) mfound.setBrand(x);
                    x = readLine("  Model [" + mfound.getModel() + "]: "); if (!x.isEmpty()) mfound.setModel(x);
                    x = readLine("  Daily Rate [" + mfound.getDailyRate() + "]: ");
                    if (!x.isEmpty()) { try { mfound.setDailyRate(Double.parseDouble(x)); } catch (NumberFormatException e) { System.out.println("  [!] Invalid, kept old."); } }
                    DataManager.saveVehicles(mvl);
                    System.out.println("  Vehicle updated!");
                    break;
                case "4":
                    String dvid = readNonEmpty("  Vehicle ID to delete: ");
                    ArrayList<Vehicle> dvl = DataManager.loadVehicles();
                    for (int i = 0; i < dvl.size(); i++) {
                        if (dvl.get(i).getVehicleId().equalsIgnoreCase(dvid)) {
                            if (dvl.get(i).getStatus().equals("Rented")) { System.out.println("  [!] Cannot delete a rented vehicle. Complete the booking first."); break; }
                            System.out.println("  " + dvl.get(i));
                            if (!readYesNo("  Delete this vehicle? (y/n): ")) break;
                            dvl.remove(i); DataManager.saveVehicles(dvl);
                            System.out.println("  Vehicle deleted."); break;
                        }
                    }
                    break;
                case "5":
                    String svid = readNonEmpty("  Vehicle ID: ");
                    ArrayList<Vehicle> svl = DataManager.loadVehicles();
                    for (Vehicle ve : svl) {
                        if (ve.getVehicleId().equalsIgnoreCase(svid)) {
                            if (ve.getStatus().equals("Rented")) { System.out.println("  [!] Cannot change rented car status. Complete the booking first."); break; }
                            System.out.println("  Current status: " + ve.getStatus());
                            String ns = readChoice("  New status (Available/Maintenance): ", new String[]{"Available", "Maintenance"});
                            ve.setStatus(ns); DataManager.saveVehicles(svl);
                            System.out.println("  Status changed to: " + ns); break;
                        }
                    }
                    break;
                case "6":
                    String uvid = readNonEmpty("  Vehicle ID: ");
                    ArrayList<Vehicle> uvl = DataManager.loadVehicles();
                    for (Vehicle ve : uvl) {
                        if (ve.getVehicleId().equalsIgnoreCase(uvid)) {
                            System.out.println("  Current mileage: " + String.format("%.0f", ve.getMileage()) + " km");
                            double nm = readDouble("  New mileage (km): ");
                            if (nm < ve.getMileage()) { System.out.println("  [!] New mileage cannot be less than current."); break; }
                            ve.setMileage(nm); DataManager.saveVehicles(uvl);
                            System.out.println("  Mileage updated to: " + String.format("%.0f", nm) + " km"); break;
                        }
                    }
                    break;
                case "7":
                    String fc = readChoice("  Category (Economy/SUV/Luxury): ", new String[]{"Economy", "SUV", "Luxury"});
                    boolean ff = false;
                    for (Vehicle ve : DataManager.loadVehicles()) if (ve.getCategory().equals(fc)) { System.out.println("  " + ve); ff = true; }
                    if (!ff) System.out.println("  No vehicles in this category.");
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== BOOKING MANAGEMENT (Staff/Manager) ====================
    private static void bookingManagement() {
        while (true) {
            System.out.println("\n--- Booking Management ---");
            System.out.println("  1. View All Bookings");
            System.out.println("  2. Create Booking for Customer");
            System.out.println("  3. Complete Booking (process return & payment)");
            System.out.println("  4. Cancel Booking");
            System.out.println("  0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    ArrayList<Booking> bks = DataManager.loadBookings();
                    if (bks.isEmpty()) { System.out.println("  No bookings yet."); break; }
                    System.out.println();
                    for (Booking b : bks) System.out.println("  " + b);
                    break;
                case "2": createBookingForCustomer(); break;
                case "3": completeBooking(); break;
                case "4": cancelBooking(); break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== CUSTOMER BOOKING (Customer view) ====================
    private static void customerBooking(Customer c) {
        while (true) {
            System.out.println("\n--- Browse & Book ---");
            System.out.println("  1. View Available Cars");
            System.out.println("  2. Book a Car");
            System.out.println("  3. Cancel My Booking");
            System.out.println("  0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    boolean any = false;
                    System.out.println();
                    for (Vehicle v : DataManager.loadVehicles()) if (v.getStatus().equals("Available")) { System.out.println("  " + v); any = true; }
                    if (!any) System.out.println("  No cars available right now. Check back later!");
                    break;
                case "2": createBooking(c); break;
                case "3":
                    System.out.println("\n  Your active bookings:");
                    boolean hasActive = false;
                    for (Booking b : DataManager.loadBookings())
                        if (b.getCustomerId().equals(c.getId()) && b.getStatus().equals("Active")) { System.out.println("  " + b); hasActive = true; }
                    if (!hasActive) { System.out.println("  No active bookings to cancel."); break; }
                    String cid = readNonEmpty("  Booking ID to cancel: ");
                    ArrayList<Booking> cbks = DataManager.loadBookings();
                    for (Booking b : cbks) {
                        if (b.getBookingId().equalsIgnoreCase(cid) && b.getCustomerId().equals(c.getId()) && b.getStatus().equals("Active")) {
                            if (!readYesNo("  Cancel booking " + cid + "? (y/n): ")) break;
                            b.setStatus("Cancelled"); DataManager.saveBookings(cbks);
                            ArrayList<Vehicle> vs = DataManager.loadVehicles();
                            for (Vehicle v : vs) if (v.getVehicleId().equals(b.getVehicleId())) { v.setStatus("Available"); break; }
                            DataManager.saveVehicles(vs);
                            System.out.println("  Booking cancelled successfully."); break;
                        }
                    }
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== CREATE BOOKING ====================
    private static void createBooking(Customer c) {
        ArrayList<Vehicle> allV = DataManager.loadVehicles();
        ArrayList<Vehicle> avail = new ArrayList<>();
        System.out.println("\n  --- Available Vehicles ---");
        for (Vehicle v : allV) if (v.getStatus().equals("Available")) { avail.add(v); System.out.println("  " + v); }
        if (avail.isEmpty()) { System.out.println("  No available vehicles."); return; }

        System.out.println();
        System.out.println("  Enter 0 to go back.");
        String vid = readNonEmpty("  Enter Vehicle ID to book: ");
        if (vid.equals("0")) { System.out.println("  Returning to menu."); return; }

        Vehicle selV = null;
        for (Vehicle v : avail) if (v.getVehicleId().equalsIgnoreCase(vid)) { selV = v; break; }
        if (selV == null) { System.out.println("  [!] Vehicle not found or not available. Try again or enter 0 to go back."); return; }

        String sd = readDate("  Start date (DD/MM/YYYY): ");
        String ed = readDate("  End date (DD/MM/YYYY): ");

        // Auto-calculate rental days from dates
        int days = 0;
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
            sdf.setLenient(false);
            java.util.Date startDate = sdf.parse(sd);
            java.util.Date endDate = sdf.parse(ed);
            long diff = endDate.getTime() - startDate.getTime();
            days = (int) (diff / (1000 * 60 * 60 * 24));
            if (days <= 0) {
                System.out.println("  [!] End date must be after start date.");
                return;
            }
        } catch (Exception e) {
            System.out.println("  [!] Error calculating dates.");
            return;
        }

        System.out.println("  Rental duration: " + days + " day(s)");
        double cost = selV.calculateRentalCost(days);

        System.out.println("\n  +------ Booking Summary ------+");
        System.out.println("  | Customer : " + c.getName());
        System.out.println("  | Vehicle  : " + selV.getBrand() + " " + selV.getModel() + " [" + selV.getCategory() + "]");
        System.out.println("  | Plate    : " + selV.getPlateNumber());
        System.out.println("  | Period   : " + sd + " to " + ed + " (" + days + " days)");
        System.out.printf("  | Cost     : RM%.2f%n", cost);
        System.out.println("  +-----------------------------+");

        if (!readYesNo("  Confirm booking? (y/n): ")) { System.out.println("  Booking cancelled."); return; }

        ArrayList<Booking> bkList = DataManager.loadBookings();
        String bid = DataManager.generateId("BK", bkList.size());
        bkList.add(new Booking(bid, c.getId(), selV.getVehicleId(), c.getName(),
                selV.getBrand() + " " + selV.getModel(), sd, ed, days, cost, "Active", today()));
        DataManager.saveBookings(bkList);
        selV.setStatus("Rented"); DataManager.saveVehicles(allV);

        System.out.println("\n  Booking confirmed!");
        System.out.println("  Booking ID : " + bid);
        System.out.printf("  Total Cost : RM%.2f%n", cost);
        System.out.println("  Status     : Active");
        System.out.println("  Note: Staff will complete & process payment when you return the car.");
    }

    private static void createBookingForCustomer() {
        System.out.println("\n  --- Create Booking for Customer ---");
        String custId = readNonEmpty("  Enter Customer ID: ");
        Customer found = null;
        for (Customer c : DataManager.loadCustomers()) if (c.getId().equalsIgnoreCase(custId)) { found = c; break; }
        if (found == null) { System.out.println("  [!] Customer not found. They must register first."); return; }
        System.out.println("  Customer: " + found.getName() + " (" + found.getId() + ")");
        createBooking(found);
    }

    // ==================== COMPLETE BOOKING (Staff/Manager) ====================
    private static void completeBooking() {
        ArrayList<Booking> bks = DataManager.loadBookings();
        ArrayList<Booking> active = new ArrayList<>();
        System.out.println("\n  --- Active Bookings ---");
        for (Booking b : bks) if (b.getStatus().equals("Active")) { active.add(b); System.out.println("  " + b); }
        if (active.isEmpty()) { System.out.println("  No active bookings to complete."); return; }

        System.out.println();
        String bid = readNonEmpty("  Booking ID to complete: ");
        Booking bk = null;
        for (Booking b : active) if (b.getBookingId().equalsIgnoreCase(bid)) { bk = b; break; }
        if (bk == null) { System.out.println("  [!] Booking not found."); return; }

        System.out.println("\n  --- Processing Return & Payment ---");
        System.out.println("  Customer: " + bk.getCustomerName());
        System.out.println("  Vehicle : " + bk.getVehicleName());
        System.out.printf("  Rental  : %d days | RM%.2f%n", bk.getRentalDays(), bk.getTotalCost());

        System.out.println("\n  --- Vehicle Inspection ---");
        String fuelLevel = readChoice("  Fuel level (Full/3quarter/Half/Quarter/Empty): ",
                new String[]{"Full", "3quarter", "Half", "Quarter", "Empty"});
        switch (fuelLevel) { case "3quarter": fuelLevel = "3/4"; break; case "Half": fuelLevel = "1/2"; break; case "Quarter": fuelLevel = "1/4"; break; }

        boolean hasDmg = readYesNo("  Any damage found? (y/n): ");
        String dmgDesc = "None"; double dmgFee = 0;
        if (hasDmg) { dmgDesc = readNonEmpty("  Damage description: "); dmgFee = readDouble("  Damage fee (RM): "); }
        int lateDays = readInt("  Late return days (0 if on time): ", 0, 365);

        // Create return record
        ArrayList<ReturnRecord> rl = DataManager.loadReturns();
        String rid = DataManager.generateId("RET", rl.size());
        ReturnRecord rec = new ReturnRecord(rid, bk.getBookingId(), today(), fuelLevel, hasDmg, dmgDesc, dmgFee, lateDays);
        rl.add(rec); DataManager.saveReturns(rl);

        // Complete booking
        bk.setStatus("Completed"); DataManager.saveBookings(bks);

        // Release vehicle
        ArrayList<Vehicle> vs = DataManager.loadVehicles();
        for (Vehicle v : vs) if (v.getVehicleId().equals(bk.getVehicleId())) { v.setStatus("Available"); break; }
        DataManager.saveVehicles(vs);

        // Record payment
        double penalty = rec.getTotalPenalty();
        double grandTotal = bk.getTotalCost() + penalty;
        CompanyBalance.addRevenue(grandTotal);
        DataManager.saveBalance();

        // Print invoice
        System.out.println("\n  ============================================");
        System.out.println("              PAYMENT INVOICE");
        System.out.println("  ============================================");
        System.out.println("  Booking ID   : " + bk.getBookingId());
        System.out.println("  Return ID    : " + rid);
        System.out.println("  Date         : " + today());
        System.out.println("  Customer     : " + bk.getCustomerName());
        System.out.println("  Vehicle      : " + bk.getVehicleName());
        System.out.println("  Period       : " + bk.getStartDate() + " to " + bk.getEndDate());
        System.out.printf("  Rental Cost  : RM%.2f (%d days)%n", bk.getTotalCost(), bk.getRentalDays());
        System.out.println("  --------------------------------------------");
        System.out.println("  Fuel Level   : " + fuelLevel);
        System.out.printf("  Fuel Penalty : RM%.2f%n", rec.getFuelPenalty());
        System.out.println("  Damage       : " + (hasDmg ? dmgDesc : "None"));
        System.out.printf("  Damage Fee   : RM%.2f%n", dmgFee);
        System.out.printf("  Late Fee     : RM%.2f (%d days x RM%.2f)%n", rec.getLateFee(), lateDays, ReturnRecord.LATE_FEE_PER_DAY);
        System.out.printf("  Total Penalty: RM%.2f%n", penalty);
        System.out.println("  ============================================");
        System.out.printf("  GRAND TOTAL  : RM%.2f%n", grandTotal);
        System.out.println("  ============================================");
        System.out.println("  Payment recorded. Booking completed!");
        System.out.println("  Customer can now write a review for this booking.");
    }

    private static void cancelBooking() {
        String cid = readNonEmpty("  Booking ID to cancel: ");
        ArrayList<Booking> cbks = DataManager.loadBookings();
        for (Booking b : cbks) {
            if (b.getBookingId().equalsIgnoreCase(cid) && b.getStatus().equals("Active")) {
                System.out.println("  " + b);
                if (!readYesNo("  Cancel this booking? (y/n): ")) return;
                b.setStatus("Cancelled"); DataManager.saveBookings(cbks);
                ArrayList<Vehicle> vs = DataManager.loadVehicles();
                for (Vehicle v : vs) if (v.getVehicleId().equals(b.getVehicleId())) { v.setStatus("Available"); break; }
                DataManager.saveVehicles(vs);
                System.out.println("  Booking cancelled."); return;
            }
        }
        System.out.println("  [!] Active booking not found with that ID.");
    }

    // ==================== MY BOOKINGS ====================
    private static void viewMyBookings(Customer c) {
        System.out.println("\n--- My Bookings ---");
        boolean any = false;
        for (Booking b : DataManager.loadBookings()) {
            if (b.getCustomerId().equals(c.getId())) { System.out.println("  " + b); any = true; }
        }
        if (!any) System.out.println("  You have no bookings yet. Go to 'Browse & Book Cars' to make one!");
    }

    // ==================== CUSTOMER CRUD ====================
    private static void customerCRUD() {
        while (true) {
            System.out.println("\n--- Customer Management ---");
            System.out.println("  1. View All   2. Modify   3. Delete   4. Search   0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    ArrayList<Customer> cl = DataManager.loadCustomers();
                    if (cl.isEmpty()) System.out.println("  No customers registered yet.");
                    else { for (Customer c : cl) System.out.println("  " + c); System.out.println("  --- Total: " + cl.size() + " ---"); }
                    break;
                case "2":
                    String mid = readNonEmpty("  Customer ID: ");
                    ArrayList<Customer> mcl = DataManager.loadCustomers();
                    Customer mf = null;
                    for (Customer c : mcl) if (c.getId().equalsIgnoreCase(mid)) { mf = c; break; }
                    if (mf == null) { System.out.println("  [!] Not found."); break; }
                    System.out.println("  Current: " + mf);
                    System.out.println("  (Press Enter to keep current value)\n");
                    String v = readLine("  Name [" + mf.getName() + "]: "); if (!v.isEmpty()) mf.setName(v);
                    v = readLine("  Phone [" + mf.getPhone() + "]: ");
                    if (!v.isEmpty()) { if (v.matches("[0-9\\-\\+]{7,15}")) mf.setPhone(v); else System.out.println("  [!] Invalid phone, kept old."); }
                    v = readLine("  Email [" + mf.getEmail() + "]: ");
                    if (!v.isEmpty()) { if (v.contains("@") && v.contains(".")) mf.setEmail(v); else System.out.println("  [!] Invalid email, kept old."); }
                    DataManager.saveCustomers(mcl);
                    System.out.println("  Customer updated!");
                    break;
                case "3":
                    String did = readNonEmpty("  Customer ID: ");
                    ArrayList<Customer> dcl = DataManager.loadCustomers();
                    for (int i = 0; i < dcl.size(); i++) {
                        if (dcl.get(i).getId().equalsIgnoreCase(did)) {
                            if (!readYesNo("  Delete " + dcl.get(i).getName() + "? (y/n): ")) break;
                            dcl.remove(i); DataManager.saveCustomers(dcl);
                            System.out.println("  Deleted!"); break;
                        }
                    }
                    break;
                case "4":
                    String kw = readNonEmpty("  Keyword: ").toLowerCase();
                    boolean sf = false;
                    for (Customer c : DataManager.loadCustomers())
                        if (c.getName().toLowerCase().contains(kw) || c.getId().toLowerCase().contains(kw) || c.getUsername().toLowerCase().contains(kw)) { System.out.println("  " + c); sf = true; }
                    if (!sf) System.out.println("  No results.");
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== SUPPLIER CRUD ====================
    private static void supplierCRUD() {
        while (true) {
            System.out.println("\n--- Supplier Management ---");
            System.out.println("  1. View All   2. Add   3. Modify   4. Delete   0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    ArrayList<Supplier> sl = DataManager.loadSuppliers();
                    if (sl.isEmpty()) System.out.println("  No suppliers yet.");
                    else for (Supplier s : sl) System.out.println("  " + s);
                    break;
                case "2":
                    System.out.println("\n  --- Add Supplier ---");
                    String cn = readNonEmpty("  Company Name: ");
                    String cp = readNonEmpty("  Contact Person: ");
                    String ph = readPhone("  Phone: ");
                    String em = readEmail("  Email: ");
                    String tp = readChoice("  Service Type (Parts/Maintenance/Tyre/Insurance): ", new String[]{"Parts", "Maintenance", "Tyre", "Insurance"});
                    ArrayList<Supplier> asl = DataManager.loadSuppliers();
                    String sid = DataManager.generateId("SUP", asl.size());
                    asl.add(new Supplier(sid, cn, cp, ph, em, tp)); DataManager.saveSuppliers(asl);
                    System.out.println("  Supplier added! ID: " + sid);
                    break;
                case "3":
                    String msid = readNonEmpty("  Supplier ID: ");
                    ArrayList<Supplier> msl = DataManager.loadSuppliers();
                    Supplier msf = null;
                    for (Supplier s : msl) if (s.getSupplierId().equalsIgnoreCase(msid)) { msf = s; break; }
                    if (msf == null) { System.out.println("  [!] Not found."); break; }
                    System.out.println("  (Press Enter to keep current value)\n");
                    String sv = readLine("  Company [" + msf.getCompanyName() + "]: "); if (!sv.isEmpty()) msf.setCompanyName(sv);
                    sv = readLine("  Contact [" + msf.getContactPerson() + "]: "); if (!sv.isEmpty()) msf.setContactPerson(sv);
                    sv = readLine("  Phone [" + msf.getPhone() + "]: ");
                    if (!sv.isEmpty()) { if (sv.matches("[0-9\\-\\+]{7,15}")) msf.setPhone(sv); else System.out.println("  [!] Invalid, kept old."); }
                    DataManager.saveSuppliers(msl);
                    System.out.println("  Supplier updated!");
                    break;
                case "4":
                    String dsid = readNonEmpty("  Supplier ID: ");
                    ArrayList<Supplier> dsl = DataManager.loadSuppliers();
                    for (int i = 0; i < dsl.size(); i++) {
                        if (dsl.get(i).getSupplierId().equalsIgnoreCase(dsid)) {
                            if (!readYesNo("  Delete " + dsl.get(i).getCompanyName() + "? (y/n): ")) break;
                            dsl.remove(i); DataManager.saveSuppliers(dsl);
                            System.out.println("  Deleted!"); break;
                        }
                    }
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== RETURN RECORDS ====================
    private static void returnMenu() {
        while (true) {
            System.out.println("\n--- Return & Logistics ---");
            System.out.println("  1. View All Returns   2. View Invoice   0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    ArrayList<ReturnRecord> rl = DataManager.loadReturns();
                    if (rl.isEmpty()) System.out.println("  No return records yet.");
                    else for (ReturnRecord r : rl) System.out.println("  " + r);
                    break;
                case "2":
                    String rid = readNonEmpty("  Return ID: ");
                    for (ReturnRecord r : DataManager.loadReturns()) {
                        if (r.getReturnId().equalsIgnoreCase(rid)) {
                            Booking bk = null;
                            for (Booking b : DataManager.loadBookings()) if (b.getBookingId().equals(r.getBookingId())) { bk = b; break; }
                            System.out.println("\n  ========== INVOICE ==========");
                            System.out.println("  Return ID  : " + r.getReturnId());
                            System.out.println("  Booking ID : " + r.getBookingId());
                            if (bk != null) {
                                System.out.println("  Customer   : " + bk.getCustomerName());
                                System.out.println("  Vehicle    : " + bk.getVehicleName());
                                System.out.printf("  Rental     : %d days | RM%.2f%n", bk.getRentalDays(), bk.getTotalCost());
                            }
                            System.out.println("  Return Date: " + r.getReturnDate());
                            System.out.printf("  Fuel: %s (RM%.2f) | Damage: %s (RM%.2f) | Late: %d days (RM%.2f)%n",
                                    r.getFuelLevel(), r.getFuelPenalty(), r.isHasDamage() ? r.getDamageDescription() : "None",
                                    r.getDamageFee(), r.getLateDays(), r.getLateFee());
                            System.out.printf("  Total Penalty: RM%.2f%n", r.getTotalPenalty());
                            if (bk != null) System.out.printf("  GRAND TOTAL: RM%.2f%n", bk.getTotalCost() + r.getTotalPenalty());
                            System.out.println("  ==============================");
                            break;
                        }
                    }
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== REPORTS ====================
    private static void reportsMenu() {
        while (true) {
            System.out.println("\n--- Reports & Analytics ---");
            System.out.println("  1. Revenue Report       2. Best-Selling Category");
            System.out.println("  3. Booking Summary      4. Vehicle Utilization");
            System.out.println("  5. Customer Reviews     6. Penalty Report");
            System.out.println("  0. Back");
            switch (readLine("  Choice: ")) {
                case "1":
                    DataManager.loadBalance();
                    double ta = 0, tc = 0; int ac = 0, cc = 0, xc = 0;
                    for (Booking b : DataManager.loadBookings()) switch (b.getStatus()) { case "Active": ta += b.getTotalCost(); ac++; break; case "Completed": tc += b.getTotalCost(); cc++; break; case "Cancelled": xc++; break; }
                    System.out.println("\n  ======== REVENUE REPORT ========");
                    System.out.printf("  Active Bookings   : %d (RM%.2f pending)%n", ac, ta);
                    System.out.printf("  Completed Bookings: %d (RM%.2f collected)%n", cc, tc);
                    System.out.printf("  Cancelled Bookings: %d%n", xc);
                    System.out.println("  --------------------------------");
                    System.out.printf("  Total Revenue     : RM%.2f%n", CompanyBalance.getTotalRevenue());
                    System.out.printf("  Total Expenses    : RM%.2f%n", CompanyBalance.getTotalExpenses());
                    System.out.printf("  Net Profit        : RM%.2f%n", CompanyBalance.getNetProfit());
                    System.out.printf("  Transactions      : %d%n", CompanyBalance.getTotalTransactions());
                    System.out.println("  ================================");
                    break;
                case "2":
                    java.util.HashMap<String, Integer> catMap = new java.util.HashMap<>();
                    for (Booking b : DataManager.loadBookings()) if (b.getStatus().equals("Completed"))
                        for (Vehicle v : DataManager.loadVehicles()) if (v.getVehicleId().equals(b.getVehicleId())) { catMap.put(v.getCategory(), catMap.getOrDefault(v.getCategory(), 0) + 1); break; }
                    if (catMap.isEmpty()) { System.out.println("  No completed bookings yet."); break; }
                    System.out.println("\n  === Best-Selling Category ===");
                    String best = ""; int mx = 0;
                    for (java.util.Map.Entry<String, Integer> e : catMap.entrySet()) { System.out.println("  " + e.getKey() + ": " + e.getValue() + " bookings"); if (e.getValue() > mx) { mx = e.getValue(); best = e.getKey(); } }
                    System.out.println("  Winner: " + best + " (" + mx + " bookings)");
                    break;
                case "3":
                    ArrayList<Booking> sbks = DataManager.loadBookings();
                    if (sbks.isEmpty()) { System.out.println("  No bookings."); break; }
                    double tr = 0; for (Booking b : sbks) { System.out.println("  " + b); if (b.getStatus().equals("Completed")) tr += b.getTotalCost(); }
                    System.out.printf("\n  Total: %d bookings | Completed revenue: RM%.2f%n", sbks.size(), tr);
                    break;
                case "4":
                    java.util.HashMap<String, Integer> vc = new java.util.HashMap<>();
                    for (Booking b : DataManager.loadBookings()) if (!b.getStatus().equals("Cancelled")) vc.put(b.getVehicleId(), vc.getOrDefault(b.getVehicleId(), 0) + 1);
                    String topCar = ""; int topN = 0;
                    for (Vehicle v : DataManager.loadVehicles()) { int cnt = vc.getOrDefault(v.getVehicleId(), 0); System.out.println("  " + v.getVehicleId() + " " + v.getBrand() + " " + v.getModel() + " - " + cnt + " bookings"); if (cnt > topN) { topN = cnt; topCar = v.getBrand() + " " + v.getModel(); } }
                    if (!topCar.isEmpty()) System.out.println("  Most rented: " + topCar + " (" + topN + ")");
                    break;
                case "5":
                    ArrayList<Review> revs = DataManager.loadReviews();
                    if (revs.isEmpty()) { System.out.println("  No reviews yet."); break; }
                    double rt = 0; for (Review r : revs) { System.out.println("  " + r); rt += r.getRating(); }
                    System.out.printf("  Average: %.1f/5.0 (%d reviews)%n", rt / revs.size(), revs.size());
                    break;
                case "6":
                    ArrayList<ReturnRecord> rets = DataManager.loadReturns();
                    if (rets.isEmpty()) { System.out.println("  No returns."); break; }
                    double tp = 0; int dc = 0, lc = 0;
                    for (ReturnRecord r : rets) { if (r.getTotalPenalty() > 0) System.out.println("  " + r); tp += r.getTotalPenalty(); if (r.isHasDamage()) dc++; if (r.getLateDays() > 0) lc++; }
                    System.out.printf("  Returns: %d | Damages: %d | Late: %d | Penalties: RM%.2f%n", rets.size(), dc, lc, tp);
                    break;
                case "0": return;
                default: System.out.println("  [!] Invalid choice.");
            }
        }
    }

    // ==================== BALANCE ====================
    private static void showBalance() {
        DataManager.loadBalance();
        System.out.println("\n  ======= Company Balance =======");
        System.out.printf("  Revenue     : RM%.2f%n", CompanyBalance.getTotalRevenue());
        System.out.printf("  Expenses    : RM%.2f%n", CompanyBalance.getTotalExpenses());
        System.out.printf("  Net Profit  : RM%.2f%n", CompanyBalance.getNetProfit());
        System.out.printf("  Transactions: %d%n", CompanyBalance.getTotalTransactions());
        System.out.println("  ===============================");
    }

    // ==================== PROFILES ====================
    private static void editStaffProfile(Staff s) {
        System.out.println("\n--- My Profile ---");
        System.out.println("  " + s);
        System.out.println("  (Press Enter to keep current value)\n");
        String v = readLine("  Name [" + s.getName() + "]: "); if (!v.isEmpty()) s.setName(v);
        v = readLine("  Phone [" + s.getPhone() + "]: ");
        if (!v.isEmpty()) { if (v.matches("[0-9\\-\\+]{7,15}")) s.setPhone(v); else System.out.println("  [!] Invalid, kept old."); }
        v = readLine("  New password (blank to keep): "); if (!v.isEmpty()) s.setPassword(v);
        ArrayList<Staff> sl = DataManager.loadStaff();
        for (Staff st : sl) if (st.getId().equals(s.getId())) { st.setName(s.getName()); st.setPhone(s.getPhone()); st.setPassword(s.getPassword()); break; }
        DataManager.saveStaff(sl);
        System.out.println("  Profile updated!");
    }

    private static void editCustomerProfile(Customer c) {
        System.out.println("\n--- My Profile ---");
        System.out.println("  " + c);
        System.out.println("  (Press Enter to keep current value)\n");
        String v = readLine("  Name [" + c.getName() + "]: "); if (!v.isEmpty()) c.setName(v);
        v = readLine("  Phone [" + c.getPhone() + "]: ");
        if (!v.isEmpty()) { if (v.matches("[0-9\\-\\+]{7,15}")) c.setPhone(v); else System.out.println("  [!] Invalid, kept old."); }
        v = readLine("  Email [" + c.getEmail() + "]: ");
        if (!v.isEmpty()) { if (v.contains("@") && v.contains(".")) c.setEmail(v); else System.out.println("  [!] Invalid, kept old."); }
        v = readLine("  New password (blank to keep): "); if (!v.isEmpty()) c.setPassword(v);
        ArrayList<Customer> cl = DataManager.loadCustomers();
        for (Customer cu : cl) if (cu.getId().equals(c.getId())) { cu.setName(c.getName()); cu.setPhone(c.getPhone()); cu.setEmail(c.getEmail()); cu.setPassword(c.getPassword()); break; }
        DataManager.saveCustomers(cl);
        System.out.println("  Profile updated!");
    }

    // ==================== WRITE REVIEW ====================
    private static void writeReview(Customer c) {
        System.out.println("\n--- Write a Review ---");
        ArrayList<Booking> comp = new ArrayList<>();
        for (Booking b : DataManager.loadBookings())
            if (b.getCustomerId().equals(c.getId()) && b.getStatus().equals("Completed")) comp.add(b);

        if (comp.isEmpty()) {
            System.out.println("  You have no completed bookings to review.");
            System.out.println("  A booking must be completed by staff before you can write a review.");
            System.out.println("  Workflow: Book a car -> Return the car -> Staff completes booking -> You write review.");
            return;
        }

        System.out.println("  Your completed bookings:\n");
        for (Booking b : comp) System.out.println("  " + b.getBookingId() + " - " + b.getVehicleName() + " (" + b.getStartDate() + " to " + b.getEndDate() + ")");

        System.out.println();
        String bid = readNonEmpty("  Enter Booking ID to review: ");
        Booking bk = null;
        for (Booking b : comp) if (b.getBookingId().equalsIgnoreCase(bid)) { bk = b; break; }
        if (bk == null) { System.out.println("  [!] Booking not found in your completed list."); return; }

        // Check duplicate review
        for (Review r : DataManager.loadReviews())
            if (r.getCustomerId().equals(c.getId()) && r.getVehicleId().equals(bk.getVehicleId())) {
                System.out.println("  [!] You already reviewed this vehicle."); return;
            }

        int rating = readInt("  Rating (1=Poor, 2=Fair, 3=Good, 4=Great, 5=Excellent): ", 1, 5);
        String comment = readNonEmpty("  Your comment: ");

        ArrayList<Review> rl = DataManager.loadReviews();
        String rid = DataManager.generateId("REV", rl.size());
        rl.add(new Review(rid, c.getId(), bk.getVehicleId(), rating, comment, today()));
        DataManager.saveReviews(rl);
        System.out.println("  Thank you! Your review has been submitted.");
    }
}
