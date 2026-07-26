package carrentalsystem;

import java.io.*;
import java.util.ArrayList;

public class DataManager {
    private static final String DIR = "data";
    private static final String SEP = "||";
    private static final String SP = "\\|\\|";

    public static void initDataDirectory() { new File(DIR).mkdirs(); }

    public static String generateId(String prefix, int count) { return String.format("%s%03d", prefix, count + 1); }

    // ===== MANAGERS =====
    public static ArrayList<Manager> loadManagers() {
        ArrayList<Manager> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/managers.txt"))) {
            String line;
            while ((line = br.readLine()) != null) {
                String[] p = line.split(SP);
                if (p.length >= 6) list.add(new Manager(p[0].trim(), p[1].trim(), p[2].trim(), p[3].trim(), p[4].trim(), p[5].trim()));
            }
        } catch (FileNotFoundException e) {
            list.add(new Manager("MGR001", "admin", "admin123", "System Admin", "0123456789", "admin@carrental.com"));
            saveManagers(list);
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveManagers(ArrayList<Manager> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/managers.txt"))) {
            for (Manager m : list) pw.println(m.getId() + SEP + m.getUsername() + SEP + m.getPassword() + SEP + m.getName() + SEP + m.getPhone() + SEP + m.getEmail());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== STAFF =====
    public static ArrayList<Staff> loadStaff() {
        ArrayList<Staff> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/staff.txt"))) {
            String line;
            while ((line = br.readLine()) != null) {
                String[] p = line.split(SP);
                if (p.length >= 8) list.add(new Staff(p[0].trim(), p[1].trim(), p[2].trim(), p[3].trim(), p[4].trim(), p[5].trim(), p[6].trim(), Double.parseDouble(p[7].trim())));
            }
        } catch (FileNotFoundException e) {
            // no staff yet
        } catch (IOException | NumberFormatException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveStaff(ArrayList<Staff> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/staff.txt"))) {
            for (Staff s : list) pw.println(s.getId() + SEP + s.getUsername() + SEP + s.getPassword() + SEP + s.getName() + SEP + s.getPhone() + SEP + s.getEmail() + SEP + s.getPosition() + SEP + s.getSalary());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== CUSTOMERS =====
    public static ArrayList<Customer> loadCustomers() {
        ArrayList<Customer> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/customers.txt"))) {
            String line;
            while ((line = br.readLine()) != null) {
                String[] p = line.split(SP);
                if (p.length >= 9) {
                    License lic = new License(p[6].trim(), p[7].trim(), p[8].trim());
                    list.add(new Customer(p[0].trim(), p[1].trim(), p[2].trim(), p[3].trim(), p[4].trim(), p[5].trim(), lic));
                }
            }
        } catch (FileNotFoundException e) {
            // no customers yet
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveCustomers(ArrayList<Customer> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/customers.txt"))) {
            for (Customer c : list) {
                License l = c.getLicense();
                String ln = "", lt = "", le = "";
                if (l != null) { ln = l.getLicenseNumber(); lt = l.getLicenseType(); le = l.getExpiryDate(); }
                pw.println(c.getId() + SEP + c.getUsername() + SEP + c.getPassword() + SEP + c.getName() + SEP + c.getPhone() + SEP + c.getEmail() + SEP + ln + SEP + lt + SEP + le);
            }
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== VEHICLES =====
    public static ArrayList<Vehicle> loadVehicles() {
        ArrayList<Vehicle> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/vehicles.txt"))) {
            String line;
            while ((line = br.readLine()) != null) {
                String[] p = line.split(SP);
                if (p.length >= 12) {
                    String cat = p[11].trim();
                    Vehicle v;
                    switch (cat) {
                        case "SUV": v = new SUVCar(p[0].trim(), p[1].trim(), p[2].trim(), Integer.parseInt(p[3].trim()), p[4].trim(), p[5].trim(), Integer.parseInt(p[6].trim()), Double.parseDouble(p[7].trim()), p[8].trim(), Double.parseDouble(p[9].trim()), Boolean.parseBoolean(p[10].trim())); break;
                        case "Luxury": v = new LuxuryCar(p[0].trim(), p[1].trim(), p[2].trim(), Integer.parseInt(p[3].trim()), p[4].trim(), p[5].trim(), Integer.parseInt(p[6].trim()), Double.parseDouble(p[7].trim()), p[8].trim(), Double.parseDouble(p[9].trim()), Boolean.parseBoolean(p[10].trim())); break;
                        default: v = new EconomyCar(p[0].trim(), p[1].trim(), p[2].trim(), Integer.parseInt(p[3].trim()), p[4].trim(), p[5].trim(), Integer.parseInt(p[6].trim()), Double.parseDouble(p[7].trim()), p[8].trim(), Double.parseDouble(p[9].trim()), Boolean.parseBoolean(p[10].trim())); break;
                    }
                    list.add(v);
                }
            }
        } catch (FileNotFoundException e) {
            list = createSampleVehicles(); saveVehicles(list);
        } catch (IOException | NumberFormatException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveVehicles(ArrayList<Vehicle> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/vehicles.txt"))) {
            for (Vehicle v : list) {
                String extra = "false";
                if (v instanceof EconomyCar) extra = String.valueOf(((EconomyCar) v).isHasAirCon());
                else if (v instanceof SUVCar) extra = String.valueOf(((SUVCar) v).isFourWheelDrive());
                else if (v instanceof LuxuryCar) extra = String.valueOf(((LuxuryCar) v).isHasChauffeur());
                pw.println(v.getVehicleId() + SEP + v.getBrand() + SEP + v.getModel() + SEP + v.getYear() + SEP + v.getPlateNumber() + SEP + v.getTransmissionType() + SEP + v.getSeatingCapacity() + SEP + v.getMileage() + SEP + v.getStatus() + SEP + v.getDailyRate() + SEP + extra + SEP + v.getCategory());
            }
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== BOOKINGS =====
    public static ArrayList<Booking> loadBookings() {
        ArrayList<Booking> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/bookings.txt"))) {
            String line;
            while ((line = br.readLine()) != null) {
                String[] p = line.split(SP);
                if (p.length >= 11) list.add(new Booking(p[0].trim(), p[1].trim(), p[2].trim(), p[3].trim(), p[4].trim(), p[5].trim(), p[6].trim(), Integer.parseInt(p[7].trim()), Double.parseDouble(p[8].trim()), p[9].trim(), p[10].trim()));
            }
        } catch (FileNotFoundException e) {} catch (IOException | NumberFormatException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveBookings(ArrayList<Booking> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/bookings.txt"))) {
            for (Booking b : list) pw.println(b.getBookingId() + SEP + b.getCustomerId() + SEP + b.getVehicleId() + SEP + b.getCustomerName() + SEP + b.getVehicleName() + SEP + b.getStartDate() + SEP + b.getEndDate() + SEP + b.getRentalDays() + SEP + b.getTotalCost() + SEP + b.getStatus() + SEP + b.getBookingDate());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== SUPPLIERS =====
    public static ArrayList<Supplier> loadSuppliers() {
        ArrayList<Supplier> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/suppliers.txt"))) {
            String line; while ((line = br.readLine()) != null) { String[] p = line.split(SP); if (p.length >= 6) list.add(new Supplier(p[0].trim(), p[1].trim(), p[2].trim(), p[3].trim(), p[4].trim(), p[5].trim())); }
        } catch (FileNotFoundException e) {} catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveSuppliers(ArrayList<Supplier> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/suppliers.txt"))) {
            for (Supplier s : list) pw.println(s.getSupplierId() + SEP + s.getCompanyName() + SEP + s.getContactPerson() + SEP + s.getPhone() + SEP + s.getEmail() + SEP + s.getServiceType());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== RETURNS =====
    public static ArrayList<ReturnRecord> loadReturns() {
        ArrayList<ReturnRecord> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/returns.txt"))) {
            String line; while ((line = br.readLine()) != null) { String[] p = line.split(SP); if (p.length >= 8) list.add(new ReturnRecord(p[0].trim(), p[1].trim(), p[2].trim(), p[3].trim(), Boolean.parseBoolean(p[4].trim()), p[5].trim(), Double.parseDouble(p[6].trim()), Integer.parseInt(p[7].trim()))); }
        } catch (FileNotFoundException e) {} catch (IOException | NumberFormatException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveReturns(ArrayList<ReturnRecord> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/returns.txt"))) {
            for (ReturnRecord r : list) pw.println(r.getReturnId() + SEP + r.getBookingId() + SEP + r.getReturnDate() + SEP + r.getFuelLevel() + SEP + r.isHasDamage() + SEP + r.getDamageDescription() + SEP + r.getDamageFee() + SEP + r.getLateDays());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== REVIEWS =====
    public static ArrayList<Review> loadReviews() {
        ArrayList<Review> list = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/reviews.txt"))) {
            String line; while ((line = br.readLine()) != null) { String[] p = line.split(SP); if (p.length >= 6) list.add(new Review(p[0].trim(), p[1].trim(), p[2].trim(), Integer.parseInt(p[3].trim()), p[4].trim(), p[5].trim())); }
        } catch (FileNotFoundException e) {} catch (IOException | NumberFormatException e) { System.out.println("Error: " + e.getMessage()); }
        return list;
    }

    public static void saveReviews(ArrayList<Review> list) {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/reviews.txt"))) {
            for (Review r : list) pw.println(r.getReviewId() + SEP + r.getCustomerId() + SEP + r.getVehicleId() + SEP + r.getRating() + SEP + r.getComment() + SEP + r.getReviewDate());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    // ===== BALANCE =====
    public static void loadBalance() {
        try (BufferedReader br = new BufferedReader(new FileReader(DIR + "/balance.txt"))) {
            String line = br.readLine();
            if (line != null) { String[] p = line.split(SP); if (p.length >= 3) { CompanyBalance.setTotalRevenue(Double.parseDouble(p[0].trim())); CompanyBalance.setTotalExpenses(Double.parseDouble(p[1].trim())); CompanyBalance.setTotalTransactions(Integer.parseInt(p[2].trim())); } }
        } catch (FileNotFoundException e) {} catch (IOException | NumberFormatException e) { System.out.println("Error: " + e.getMessage()); }
    }

    public static void saveBalance() {
        initDataDirectory();
        try (PrintWriter pw = new PrintWriter(new FileWriter(DIR + "/balance.txt"))) {
            pw.println(CompanyBalance.getTotalRevenue() + SEP + CompanyBalance.getTotalExpenses() + SEP + CompanyBalance.getTotalTransactions());
        } catch (IOException e) { System.out.println("Error: " + e.getMessage()); }
    }

    private static ArrayList<Vehicle> createSampleVehicles() {
        ArrayList<Vehicle> v = new ArrayList<>();
        v.add(new EconomyCar("V001", "Perodua", "Myvi", 2023, "WA1234", "Auto", 5, 15000, "Available", 80.0, true));
        v.add(new EconomyCar("V002", "Proton", "Saga", 2022, "WB5678", "Manual", 5, 25000, "Available", 70.0, true));
        v.add(new EconomyCar("V003", "Honda", "City", 2024, "WC9012", "Auto", 5, 5000, "Available", 120.0, true));
        v.add(new SUVCar("V004", "Toyota", "Fortuner", 2023, "WD3456", "Auto", 7, 20000, "Available", 200.0, true));
        v.add(new SUVCar("V005", "Proton", "X70", 2024, "WE7890", "Auto", 5, 8000, "Available", 180.0, false));
        v.add(new LuxuryCar("V006", "Mercedes", "E-Class", 2024, "WF1357", "Auto", 5, 3000, "Available", 450.0, false));
        v.add(new LuxuryCar("V007", "BMW", "7-Series", 2023, "WG2468", "Auto", 5, 12000, "Available", 500.0, true));
        return v;
    }
}
