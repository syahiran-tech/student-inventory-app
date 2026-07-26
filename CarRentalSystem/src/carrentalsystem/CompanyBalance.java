package carrentalsystem;

public class CompanyBalance {
    private static double totalRevenue = 0.0;
    private static double totalExpenses = 0.0;
    private static int totalTransactions = 0;

    private CompanyBalance() {}

    public static double getTotalRevenue() { return totalRevenue; }
    public static double getTotalExpenses() { return totalExpenses; }
    public static double getNetProfit() { return totalRevenue - totalExpenses; }
    public static int getTotalTransactions() { return totalTransactions; }
    public static void addRevenue(double amount) { totalRevenue += amount; totalTransactions++; }
    public static void addExpense(double amount) { totalExpenses += amount; }
    public static void setTotalRevenue(double v) { totalRevenue = v; }
    public static void setTotalExpenses(double v) { totalExpenses = v; }
    public static void setTotalTransactions(int v) { totalTransactions = v; }

    public static String getBalanceSummary() {
        return String.format("Revenue: RM%.2f | Expenses: RM%.2f | Profit: RM%.2f | Transactions: %d",
                totalRevenue, totalExpenses, getNetProfit(), totalTransactions);
    }
}
